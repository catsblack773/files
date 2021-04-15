<template>
	<input
		type="text"
		class="el-input__inner autofocus"
		ref="inputFocus"
		v-model="value"
	/>
</template>

<script>
export default {
	data () {
		return {
			value: ''
		}
	},
	mounted () {
		this.$refs.inputFocus.focus()
	},
	watch: {
		value (val) {
			this.$emit('onUpdate', val)
		}
	}
}
</script>