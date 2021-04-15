<template>
	<div
		class="item"
		:class="{active: isActive}"
		@click.prevent="onClick"
		@dblclick="onOpen"
	>
		<div class="item-info-wrapp" @contextmenu.prevent="onContextMenu">
			<i class="el-icon-folder"></i>
			<span>{{item.name}}</span>
		</div>
		<contextmenuComponent
			:is-view="isMenu"
			:coords="coords"
			:contextmenu="contextmenu"
			v-if="isMenu"
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
		contextmenuComponent,
	},
	methods: {
		onClick (e) {
			if (e.target.closest('.el-menu-item')) return

			this.isActive = !this.isActive
			if (e.shiftKey) {
				this.selectAll(e)
			} else {
				this.$emit('activeFolders', {
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
				this.$parent.activeFolders.forEach(path => {
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
				this.$emit('activeFolders', {
					isActive: false,
					path: el.dataset.path
				})
			} else {
				this.$emit('activeFolders', {
					isActive: true,
					path: el.dataset.path
				})
			}
		},
		onOpen () {
			this.$emit('openFolder', 'open-folder', this.item.path)
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
		cursor: pointer;
		position: relative;
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
	}
</style>