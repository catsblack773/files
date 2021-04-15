<template>
	<div
		id="app"
		v-loading.fullscreen.lock="$store.getters.isLoad"
		:style="`background-image: url(${background})`"
	>
		<main-layout :apps="activeApps">
			<div class="window-desktop">
				<div class="window-desktop-content" :data-apps-type="app.type" :key="app.id" v-for="app in apps">
					<modal-content
						ref="modals"
						:path="app.path"
						:data="app"
						@closeApp="closeApp"
					/>
				</div>
			</div>
		</main-layout>
	</div>
</template>

<script>
import mainLayout from '@/layout/Main'
import modalContent from '@/components/modals/MainModal'

export default {
	data () {
		return {
			isLoad: false,
			background: '/images/background/1.jpg',
			apps: [
				{
					id: 1,
					type: 'files',
					name: 'Файловый менеджер',
					path: 'Main',
					isActive: false
				},
				{
					id: 2,
					type: 'other',
					name: 'Ещё какой-то модуль',
					path: 'Main',
					isActive: false
				}
			],
			activeApps: []
		}
	},
	components: {
		mainLayout,
		modalContent
	},
	methods: {
		activeModal (type) {
			this.$refs.modals.forEach(el => {
				if (el.data.type === type) {
					el.activeWindow = true
				} else {
					el.activeWindow = false
				}
			})
			this.activeApps.forEach((v, k) => {
				if (v.type === type) {
					this.activeApps[k].isActive = true
				} else {
					this.activeApps[k].isActive = false
				}
			})
		},
		closeApp (type) {
			this.activeApps = this.activeApps.filter(app => app.type !== type)
		},
		openApp (type) {
			this.apps.forEach(v => {
				if (v.type === type) {
					if (this.activeApps.length) {
						for (let i = 0; i < this.activeApps.length; i++) {
							if (this.activeApps[i].type === type) {
								return
							}
						}
					}
					this.activeApps.push(v)
					return
				}
			})

			this.$refs.modals.forEach(el => {
				if (el.data.type === type) {
					el.isShow = true
				}
			})
		}
	},
	mounted () {
		this.$on('windowClick', this.activeModal)
		this.$on('activeModal', this.activeModal)
		this.$on('openApp', this.openApp)
	}
}
</script>

<style>
	* {
		padding: 0;
		margin: 0;
		box-sizing: border-box;
	}
	.animated {
        -webkit-animation-duration: .75s;
        animation-duration: .75s;
    }
	#app {
		height: 100vh;
		width: 100%;
		margin: 0;
		padding: 0;
		border: 0;
		background-color: #efefef;
		background-position: center;
		background-repeat: no-repeat;
		background-size: cover;
		box-sizing: border-box;
		position: relative;
		overflow: hidden;
	}
	#app .vm--container {
		width: 0;
		height: 0;
		z-index: 999;
		position: absolute;
		top: unset;
		left: unset;
	}
	#app .vm--overlay {
		display: none;
	}
	#app .vm--modal {
		position: fixed;
	}
	#app .vm--modal.window-size-max {
        height: 100vh!important;
        width: 100vw!important;
		top: 0!important;
		left: 0!important;
    }
	.el-menu--horizontal>.el-menu-item {
		color: #3f3f3f!important;
	}
	.upload .el-upload {
		display: block;
	}
	.upload .el-upload-list {
		height: 170px;
		margin-top: 10px;
		overflow-x: hidden;
		overflow-y: auto;
	}
</style>
