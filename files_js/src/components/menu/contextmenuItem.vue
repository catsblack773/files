<template>
    <li :class="{'is-disabled': !isActive}" @click="onClick">{{item.name}}</li>
</template>

<script>
export default {
    props: {
        item: {
            type: Object,
            required: true
        }
    },
    computed: {
        isActive () {
            return this.item.isActive
        }
    },
    methods: {
        onClick () {
            this.$emit('onAction', this.item.type)
        }
    }
}
</script>