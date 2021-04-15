<template>
	<div class="main-layout">
		<slot></slot>
		<div class="panel">
			<div class="panel-nav">
				<transition name="fade">
					<nav-bar v-if="isNavBar" @openApp="openApp"/>
				</transition>
				<el-button type="primary" icon="el-icon-menu" circle @click="openNav"></el-button>
			</div>
			<div class="window-line">
				<div
					class="window-element"
					:title="app.name"
					:data-type="app.type"
					@click="windowElementClick(app.type)"
					v-for="app in apps"
					:key="app.id"
				>{{app.name}}</div>
			</div>
			<div class="tree-bar">
				<div class="clock">{{date}}</div>
			</div>
		</div>
	</div>
</template>

<script>
import NavBar from './../components/panel/NavBar'
export default {
	props: {
		apps: {
			type: Array,
			require: true
		}
	},
	data () {
		return {
			date: '',
			isNavBar: false,

		}
	},
	components: {
		NavBar
	},
	computed: {
		newApps () {
			return this.apps.filter(app => app.isActive)
		}
	},
	methods: {
		clockViews () {
			let date = new Date
			let hours = date.getHours() < 10 ? `0${date.getHours()}` : date.getHours()
			let minutes = date.getMinutes() < 10 ? `0${date.getMinutes()}` : date.getMinutes()
			let seconds = date.getSeconds() < 10 ? `0${date.getSeconds()}` : date.getSeconds()
			this.date = hours + ':' + minutes + ':' + seconds
		},
		openNav () {
			this.isNavBar = !this.isNavBar
		},
		windowElementClick (type) {
			const el = document.querySelector('.window-line')
			const elements = el.querySelectorAll('.window-element')
			elements.forEach(v => {
				if (v.dataset.type === type) {
					v.classList.add('active')
				} else {
					v.classList.remove('active')
				}
			})
			this.$parent.$emit('windowClick', type)
		},
		openApp (type) {
			this.$parent.$emit('openApp', type)
			setTimeout(() => {
				const elements = document.querySelectorAll('.window-element')
				elements.forEach(v => {
					if (v.dataset.type === type) {
						v.classList.add('active')
					} else {
						v.classList.remove('active')
					}
				})
			}, 100)
		}
	},
	created () {
		setInterval(this.clockViews, 1000)
	},
	mounted () {
		this.$on('closeNavBar', () => this.isNavBar = false)
		this.$on('activeModal', type => this.$parent.$emit('activeModal', type))
	}
}
</script>

<style scoped>
	.panel {
		display: flex;
		width: 100%;
		justify-content: space-between;
		align-items: center;
		height: 50px;
		padding: 2px 15px;
		border-top: 1px solid #ccc;
		background-color: #fff;
		z-index: 9999;
		position: absolute;
		right: 0;
		bottom: 0;
		left: 0;
	}
	.panel-nav {
		border-right: 1px solid #efefef;
		padding-right: 15px;
		position: relative;
	}
	.window-line {
		display: flex;
		flex: 0 1 auto;
		width: 100%;
		margin-left: 15px;
	}
	.window-element {
		width: 150px;
		padding: 10px;
		margin-right: 5px;
		border: 1px solid #efefef;
		border-radius: 8px;
		background: linear-gradient(180deg, #fff, #d8e0e4);
		box-shadow: 0 20px 60px -2px rgba(27, 33, 58, .4);
		white-space: nowrap;
		overflow-x: hidden;
		text-overflow: ellipsis;
		cursor: pointer;
	}
	.window-element.active {
		background: linear-gradient(180deg, #fff, #c8dfea);
	}
	.tree-bar {
		border-left: 1px solid #efefef;
		padding-left: 15px;
	}
	.fade-enter-active, .fade-leave-active {
		transition: opacity .5s;
	}
	.fade-enter, .fade-leave-to {
		opacity: 0;
	}
</style>