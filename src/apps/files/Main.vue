<template>
	<div class="files-content" v-if="isLoad">
		<el-row type="flex" :gutter="20" align="middle" class="path-content">
			<el-col :span="2">
				<i class="el-icon-back" @click="comeBack"></i>
			</el-col>
			<el-col :span="2">
				<i class="el-icon-refresh" @click="openFolder('open-folder')"></i>
			</el-col>
			<el-col :span="20">
				<el-input size="small" v-model="path" @change="openFolder('open-folder')">
					<template slot="prepend"><i class="el-icon-folder-opened"></i></template>
				</el-input>
			</el-col>
		</el-row>
		<div class="row list-content" :style="`height: ${heigth}px`">
			<folder
				ref="folders"
				class="col"
				:class="`col-${col}`"
				:data-path="folder.path"
				:key="folder.hash"
				:item="folder"
				v-for="folder in folders"
				@openFolder="openFolder"
				@reLoad="reLoad"
				@closeContextMenu="onCloseContextMenu"
				@activeFolders="onActiveFolders"
				@copy="copy"
				@cut="cut"
				@remove="onRemove"
			/>
			<file
				ref="files"
				class="col"
				:class="`col-${col}`"
				:data-path="file.path"
				:key="file.hash"
				:item="file"
				v-for="file in files"
				@reLoad="reLoad"
				@closeContextMenu="onCloseContextMenu"
				@activeFiles="onActiveFiles"
				@copy="copy"
				@cut="cut"
				@remove="onRemove"
			/>
		</div>
		<contextmenu-component
			:is-view="isMenu"
			:coords="coords"
			:contextmenu="contextmenu"
			v-if="isMenu"
			@closeContextMenu="onCloseContextMenu"
			@create="openCreateFolder"
			@paste="paste"
			@upload="onUpload"
		/>
		<modal-create
			title="Создание папки"
			:width="300"
			:height="185"
			:minHeight="150"
			:path="path"
			@reLoad="reLoad"
		/>
		<modal-upload
			title="Загрузка файла на сервер"
			:width="330"
			:height="250"
			:minHeight="150"
			:path="path"
			@reLoad="reLoad"
		/>
	</div>
</template>

<script>
import folder from './Folder'
import file from './File'
import contextmenuComponent from './../../components/menu/contextmenu'
import modalCreate from './modals/modalCreate'
import modalUpload from './modals/uploadFiles'

export default {
	data () {
		return {
			isLoad: false,
			folders: [],
			activeFolders: [],
			files: [],
			activeFiles: [],
			path: '/files/',
			isMenu: false,
			coords: {},
			contextmenu: {},
			heigth: 0,
			col: 6
		}
	},
	computed: {
		contentHeigth () {
			return this.heigth
		},
		isPaste () {
			return this.$store.getters.clipboard.type ? true : false
		}
	},
	components: {
		folder,
		file,
		contextmenuComponent,
		modalCreate,
		modalUpload
	},
	methods: {
		async onLoad () {
			this.$store.dispatch('onLoad')
			this.openFolder('open-folder')
			this.isLoad = true
			this.$store.dispatch('onLoadClose')
		},
		async openFolder (action, path = null) {
			const data = new FormData()
			if (path) {
				data.append('path', path)
			} else {
				data.append('path', this.path)
			}
			
			await this.$http(`${this.$store.getters.domans}/files/${action}/`, {
				method: 'POST',
				headers: {'is-json': true},
				data
			})
				.then(response => {
					if (response.data.res == true) {
						this.folders = response.data.folders
						this.files = response.data.files
						this.path = response.data.path
					} else if (response.data.res == false) {
						this.$message.error(response.data.message)
					}
				})
				.catch(err => {
					this.$message.error(err)
				})
		},
		async comeBack () {
			this.$store.dispatch('onLoad')
			this.openFolder('come-back')
			this.$store.dispatch('onLoadClose')
		},
		openCreateFolder () {
			this.$modal.show('create')
		},
		async onRemove (response) {
			this.$store.dispatch('onLoad')
			const data = new FormData()
			if (response.isActive) {
				let result = []
				if (this.$refs.folders) {
					this.$refs.folders.forEach(folder => {
						if (folder.isActive === true) {
							result.push(folder.item.path)
						}
					})
				}
				if (this.$refs.files) {
					this.$refs.files.forEach(file => {
						if (file.isActive === true) {
							result.push(file.item.path)
						}
					})
				}
				data.append('pathFrom', result)
			} else {
				data.append('pathFrom', response.path)
			}
			
			data.append('pathTo', this.path)
			
			await this.$http(`${this.$store.getters.domans}/files/remove/`, {
				method: 'POST',
				headers: {'is-json': true},
				data
			})
				.then(response => {
					if (response.data.res == true) {
						this.folders = response.data.folders
						this.files = response.data.files
						this.path = response.data.path

						this.$message({
							message: response.data.message,
							type: 'success'
						})
					} else if (response.data.res == false) {
						this.$message.error(response.data.message)
					}
				})
				.catch(err => {
					this.$message.error(err)
				})

			this.$store.dispatch('onLoadClose')
		},
		copy (response) {
			const data = this.addPaste(response)
			this.$store.dispatch('setClipboard', {
				items: data,
				type: 'copy'
			})
		},
		cut (response) {
			const data = this.addPaste(response)
			this.$store.dispatch('setClipboard', {
				items: data,
				type: 'cut'
			})
		},
		addPaste (response) {
			const data = []
			if (response.isActive) {
				if (this.$refs.folders) {
					this.$refs.folders.forEach(folder => {
						if (folder.isActive === true) {
							data.push(folder.item.path)
						}
					})
				}
				if (this.$refs.files) {
					this.$refs.files.forEach(file => {
						if (file.isActive === true) {
							data.push(file.item.path)
						}
					})
				}
			} else {
				data.push(response.path)
			}
			return data
		},
		async paste () {
			if (this.$store.getters.clipboard.type) {
				let type = this.$store.getters.clipboard.type
				const data = new FormData()
				data.append(`pathFrom`, this.$store.getters.clipboard.items)
				data.append('pathTo', this.path)
				data.append('type', type)

				await this.$http(`${this.$store.getters.domans}/files/paste/`, {
					method: 'POST',
					headers: {'is-json': true},
					data
				})
					.then(response => {
						console.log(response.data)
						if (response.data.res === true) {
							this.folders = response.data.folders
							this.files = response.data.files
							this.path = response.data.path

							this.$store.dispatch('setClipboard', {})
							
							this.$message({
								message: response.data.message,
								type: 'success'
							})
						} else if (response.data.res === false) {
							this.$message.error(response.data.message)
						}
					})
					.catch(err => {
						this.$message.error(err)
					})
			}
			return false
		},
		onUpload () {
			this.$modal.show('uploadFiles')
		},
		reLoad (response) {
			this.folders = response.folders
			this.files = response.files
			this.path = response.path
		},
		onActiveFolders (data) {
			if (data.isActive) {
				for (let path of this.activeFolders) {
					if (path === data.path) {
						return
					}
				}
				this.activeFolders.push(data.path)
				this.$refs.folders.forEach(el => {
					if (el.item.path === data.path) {
						el.isActive = true
					}
				})
			} else {
				this.activeFolders = this.activeFolders.filter(path => path !== data.path)
				this.$refs.folders.forEach(el => {
					if (el.item.path === data.path) {
						el.isActive = false
					}
				})
			}
		},
		onActiveFiles (data) {
			if (data.isActive) {
				for (let path of this.activeFiles) {
					if (path === data.path) {
						return
					}
				}
				this.activeFiles.push(data.path)
				this.$refs.files.forEach(el => {
					if (el.item.path === data.path) {
						el.isActive = true
					}
				})
			} else {
				this.activeFiles = this.activeFiles.filter(path => path !== data.path)
				this.$refs.files.forEach(el => {
					if (el.item.path === data.path) {
						el.isActive = false
					}
				})
			}
		},
		onContextMenu (e) {
			if (this.isMenu) {
				this.closeContextMenu()
			} else {
				this.closeContextMenu()
				this.isMenu = true
				this.coords = {
					x: e.clientX,
					y: e.clientY
				}
			}
		},
		onCloseContextMenu () {
			this.closeContextMenu()
		},
		closeContextMenu () {
			this.isMenu = false
			if (this.$refs.folders) {
				this.$refs.folders.forEach(component => component.isMenu = false)
			}
			if (this.$refs.files) {
				this.$refs.files.forEach(component => component.isMenu = false)
			}
		},
		loadContextMenu () {
			this.contextmenu = [
				{
					id: 1,
					name: 'Вставить',
					type: 'paste',
					isActive: this.$store.getters.clipboard.type ? true : false
				},
				{
					id: 2,
					name: 'Создать папку',
					type: 'create',
					isActive: true
				},
				{
					id: 3,
					name: 'Загрузить файл',
					type: 'upload',
					isActive: true
				}
			]
		}
	},
	created () {
		this.onLoad()
		this.loadContextMenu()
	},
	mounted () {
		const el = document.querySelector('.modal-content main .content')
		this.heigth = parseInt(el.dataset.heigth)
		const element = el.querySelector('.list-content')

		element.addEventListener('click', e => {
            e.preventDefault()
			this.closeContextMenu()
        })
		
		element.addEventListener('contextmenu', e => {
			e.preventDefault()
			let content = e.target.closest('.list-content');
			if (e.target.contains(content)) {
				this.onContextMenu(e)
			}
		})
	},
	watch: {
		isPaste (val) {
			this.contextmenu.forEach(v => {
				if (v.type === 'paste') {
					v.isActive = val
				}
			})
		}
	}
}
</script>

<style scoped>
	.files-content {
		min-height: 50px;
		padding: 0 15px 15px;
	}
	.path-content {
		flex-wrap: wrap;
		margin: 4px 0;
		padding: 6px 0 5px;
		border-bottom: 1px solid #ececec;
	}
	.path-content i {
		font-size: 18px;
		color: #366cac;
		font-weight: bold;
		cursor: pointer;
	}
	.list-content {
		height: 100%;
		overflow: auto;
		overflow-x: hidden;
	}
	.list-content>div {
		height: 180px;
		position: relative;
	}
	.row {
		display: flex;
		flex-wrap: wrap;
		margin: 0 10px;
	}
	.col {
		padding: 0 7.5px;
		margin-bottom: 15px;
	}
	.col-1 {
		width: 6.25%;
	}
	.col-2 {
		width: 12.5%;
	}
	.col-3 {
		width: 16.6669%;
	}
	.col-4 {
		width: 20%;
	}
	.col-5 {
		width: 25%;
	}
	.col-6 {
		width: 33.3333%;
	}
</style>