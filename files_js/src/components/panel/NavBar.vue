<template>
    <ul class="nav-bar">
        <li class="item" @click="close('files')">Файловый менеджер</li>
        <li class="item" @click="close('other')">Ещё какой то модуль</li>
        <li class="item" @click="close('other')">Ещё какой то модуль</li>
        <li class="item" @click="close('other')">Ещё какой то модуль</li>
    </ul>
</template>

<script>
export default {
    methods: {
        close (type) {
            this.$parent.$emit('closeNavBar')
            this.$emit('openApp', type)
        }
    }
}
</script>

<style scoped>
    .nav-bar {
        width: 200px;
        height: auto;
        background-color: #fff;
        z-index: 9999;
        position: absolute;
        bottom: 55px;
        left: 0;
    }
    .nav-bar .item {
        display: block;
        padding: 10px;
        border-bottom: 1px solid #efefef;
        cursor: pointer
    }
    .nav-bar .item:last-child {
        border-bottom: 0;
    }
    .nav-bar .item:hover {
        background-color: rgba(202, 210, 211, 0.2);
    }
</style>