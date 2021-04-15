<template>
	<div class="item" :class="{active: isActive}" :title="item.name" @click.prevent="onClick" @contextmenu.prevent="onContextMenu">
		<div class="item-info-wrapp" v-if="item.type === 'image'">
			<el-image
				:src="'https://files.neworia.ru' + item.path"
				fit="cover"
				:preview-src-list="[
					'https://files.neworia.ru' + item.path
				]"
				:z-index="999999"
				style="height: 120px;"
			>
				<div slot="placeholder" class="image-slot">
					Идет загрузка<span class="dot">...</span>
				</div>
				<div slot="error" class="image-slot">
					<i class="el-icon-picture-outline"></i>
				</div>
			</el-image>
			<span>{{item.name}}</span>
		</div>
		<div class="item-info-wrapp" v-else>
			<i class="el-icon-document"></i>
			<span>{{item.name}}</span>
		</div>
		<contextmenu-component
			:is-view="isMenu"
			:coords="coords"
			:contextmenu="contextmenu"
			v-if="isMenu"
			@closeContextMenu="onClick"
			@copy="copy"
			@cut="cut"
			@remove="remove"
		/>
	</div>
</template>

<script>
import contextmenuComponent from './../../components/menu/contextmenu'

export default {
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	data () {
		return {
			isMenu: false,
			coords: {},
			contextmenu: {},
			isActive: false
		}
	}, 
	components: {
		contextmenuComponent
	},
	methods: {
		onClick (e) {
			if (e && e.target.closest('.el-menu-item')) return

			this.isActive = !this.isActive
			if (e.shiftKey) {
				this.selectAll(e)
			} else {
				this.$emit('activeFiles', {
					isActive: this.isActive,
					path: this.item.path
				})
				this.$emit('closeContextMenu')
			}
		},
		selectAll (e) {
			let rows = e.target.closest('.list-content')
			let selected = e.target.closest('.item')
			let indexStart
			let indexEnd

			for (let i = 0; i < rows.children.length; i++) {
				if (selected === rows.children[i]) {
					indexEnd = i
					break;
				}
			}

			for (let i = 0; i < rows.children.length; i++) {
				this.$parent.activeFiles.forEach(path => {
					if (path === rows.children[i].dataset.path) {
						indexStart = i
						return true
					}
				})
				if (indexStart !== undefined) {
					break
				}
			}

			if (indexStart > indexEnd) {
				for (let i = indexStart - 1; i >= indexEnd; i--) {
					this.queryActive(rows.children[i])
				}
			} else {
				for (let i = indexStart + 1; i <= indexEnd; i++) {
					this.queryActive(rows.children[i])
				}
			}
		},
		queryActive (el) {
			if (el.classList.contains('active')) {
				this.$emit('activeFiles', {
					isActive: false,
					path: el.dataset.path
				})
			} else {
				this.$emit('activeFiles', {
					isActive: true,
					path: el.dataset.path
				})
			}
		},
		onContextMenu (e) {
            if (this.isMenu) {
                this.$emit('closeContextMenu')
            } else {
                this.$emit('closeContextMenu')
                this.coords = {
                    x: e.clientX,
                    y: e.clientY
                }
                this.isMenu = true
            }
        },
		loadContextMenu () {
			this.contextmenu = [
				{
					id: 1,
					name: 'Удалить',
					type: 'remove',
					isActive: true
				},
				{
					id: 2,
					name: 'Копировать',
					type: 'copy',
					isActive: true
				},
				{
					id: 3,
					name: 'Вырезать',
					type: 'cut',
					isActive: true
				}
			]
		},
		copy () {
			this.$emit('copy', {
				path: this.item.path,
				isActive: this.isActive
			})
		},
		cut () {
			this.$emit('cut', {
				path: this.item.path,
				isActive: this.isActive
			})
		},
		remove () {
			this.$emit('remove', {
				path: this.item.path,
				isActive: this.isActive
			})
		}
	},
	created () {
		this.loadContextMenu()
	}
}
</script>

<style scoped>
	.item {
		height: 180px;
		margin: 0 auto;
		margin-bottom: 15px;
		cursor: pointer;
	}

	.item:hover {
		background-color: rgba(0, 0, 0, .1);
	}

	.item.active {
		background-color: rgba(78, 108, 137, .1);
	}

	.item i, .item span {
		display: block;
		width: 100%;
		padding: 15px;
		font-size: 70px;
		text-align: center;
		box-sizing: border-box;
	}

	.item span {
		font-size: 14px;
		font-weight: bold;
		white-space: nowrap;
		overflow-x: hidden;
		text-overflow: ellipsis;
	}
	.item-info-wrapp {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}
	.item-info-wrapp .image-slot i {
		font-size: 110px;
	}
</style>