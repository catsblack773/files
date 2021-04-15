<template>
    <div class="contextmenu-wrapp">
        <el-menu class="contextmenu-content" mode="horizontal" :style="`top: ${coords.y}px;left: ${coords.x}px`">
			<contextmenu-item class="el-menu-item" v-for="v in contextmenu" :key="v.id" :item="v" @onAction="onAction(v.type)" />
			<slot></slot>
		</el-menu>
    </div>
</template>

<script>
import contextmenuItem from './contextmenuItem'

export default {
	props: {
		isView: {
			type: Boolean,
            required: true
		},
		coords: {
			type: Object,
			required: true
		},
		contextmenu: {
			type: Array,
			required: true
		}
	},
	data () {
		return {
			isActive: true
		}
	},
	components: {
		contextmenuItem
	},
	methods: {
		onAction (type) {
			this.$emit(type)
			this.$emit('closeContextMenu')
		}
	}
}
</script>

<style>
	.contextmenu-wrapp .contextmenu-content {
		min-width: 160px;
        border-radius: 6px;
        border: 1px solid #d6d6d6;
		z-index: 999;
		position: fixed!important;
		top: -100px;
		left: 100px;
	}
	.contextmenu-content.el-menu--horizontal>.el-menu-item {
		height: 40px;
        padding: 10px 20px;
		float: none;
		line-height: unset;
		border-bottom: 1px solid #efefef;
	}
	.contextmenu-content.el-menu--horizontal>.el-menu-item:last-child {
		border-bottom: 1px solid transparent;
	}
	.contextmenu-content.el-menu--horizontal>.el-menu-item:hover {
		background-color: rgba(0, 0, 20, 0.1);
	}
	.contextmenu-content.el-menu--horizontal>.el-menu-item:first-child {
		border-radius: 4px 4px 0 0;
	}
	.contextmenu-content.el-menu--horizontal>.el-menu-item:last-child {
		border-radius: 0 0 4px 4px;
	}
</style>