<template>
    <div class="modal">
        <transition
            name="window-classes-transition"
            enter-active-class="animated animate__bounceInUp"
            leave-active-class="animated animate__backOutDown"
        >
            <div class="modal-content" v-if="isShow">
                <modal
                    :name="data.type"
                    class="modal-content"
                    draggable="header .header-title"
                    :adaptive="true"
                    :resizable="true"
                    :style="`z-index: ${activeWindow ? 99999 : 9999}`"
                    :minWidth="450"
                    :minHeight="400"
                    @resize="resize"
                >
                    <header @click="isActiveModal">
                        <div class="header-wrapp">
                            <div class="header-title">{{data.name}}</div>
                            <div class="header-action-wrapp">
                                <div class="header-minimize icon el-icon-minus" @click="minimize"></div>
                                <div class="header-full-modal icon el-icon-zoom-in" @click="zoomIn"></div>
                                <div class="header-close icon el-icon-close" @click="close"></div>
                            </div>
                        </div>
                    </header>
                    <main @click="isActiveModal" :style="`height: ${heightModal}px`">
                        <div class="content" :data-heigth="heightModal - 50">
                            <files ref="component" v-if="data.type === 'files'" />
                            <other ref="component" v-else-if="data.type === 'main'" />
                        </div>
                    </main>
                    <footer @click="isActiveModal">
                        <div class="information-panel">Панель информации</div>
                    </footer>
                </modal>
            </div>
        </transition>
    </div>
</template>

<script>

import 'animate.css'
import files from './../../apps/files/Main'
import other from './../../apps/other/Main'

export default {
    props: {
        data: {
            type: Object,
            require: true
        },
        path: {
            type: String,
            require: true
        }
    },
    data () {
        return {
            activeWindow: false,
            heightModal: 300,
            isZoomModal: false,
            isShow: false,
            show: false,
            hideTimeOut: null
        }
    },
    components: {
        files,
        other
    },
    methods: {
        zoomIn (e) {
            const mainEl = e.target.closest(`.window-desktop-content[data-apps-type="${this.data.type}"]`)
            const modal = mainEl.querySelector('.vm--modal')
            mainEl.classList.add('animated')
            if (this.isZoomModal) {
                mainEl.classList.add('animate__zoomOut')
                setTimeout(() => {
                    mainEl.classList.add('animate__zoomIn')
                    mainEl.classList.remove('animate__zoomOut')
                    mainEl.classList.remove('animate__fadeIn')
                    setTimeout(() => mainEl.classList.remove('animate__zoomIn'), 1000)
                }, 200)
                this.isZoomModal = false
                modal.classList.remove('window-size-max')
            } else {
                this.isZoomModal = true
                mainEl.classList.add('animate__zoomIn')
                setTimeout(() => mainEl.classList.remove('animate__zoomIn'), 1000)
                modal.classList.add('window-size-max')
            }
            
            let h = parseInt(modal.clientHeight)
            let w = parseInt(modal.clientWidth)
            this.heightModal = h - 100
            this.$refs.component.heigth = this.heightModal - 50
            let col = Math.floor((16 - w / 150) / 2)
            this.$refs.component.col = col < 1 ? 1 : (col > 6 ? 6 : col)
        },
        minimize () {

        },
        close () {
            this.isShow = false
            this.$emit('closeApp', this.data.type)
        },
        isActiveModal () {
            this.$parent.$emit('activeModal', this.data.type)
        },
        resize (e) {
            let h = parseInt(e.ref.clientHeight)
            let w = parseInt(e.ref.clientWidth)
            this.heightModal = h - 100
            this.$refs.component.heigth = this.heightModal - 50
            let col = Math.floor((16 - w / 150) / 2)
            this.$refs.component.col = col < 1 ? 1 : (col > 6 ? 6 : col)
        }
    },
    watch: {
        isShow (val) {
            clearTimeout(this.hideTimeOut)
            if (val === true) {
                setTimeout(() => this.$modal.show(this.data.type), 10)
            } else {
                this.hideTimeOut = setTimeout(() => this.$modal.hide(this.data.type), 1000)
            }
        }
    }
}
</script>

<style scoped>
    .modal-content footer,
    .modal-content main,
    .modal-content header {
        width: 100%;
        font-size: 1rem;
        box-sizing: border-box;
    }
    .modal-content header {
        border-bottom: 1px solid #ccc;
    }
    .header-title {
        width: calc(100% - 80px);
        padding: 15px 0 15px 15px;
        cursor: move;
    }
    .header-action-wrapp {
        padding: 15px 15px 15px 0;
    }
    .modal-content footer {
        border-top: 1px solid #ccc;
    }
    .modal-content .information-panel,
    .modal-content .header-wrapp {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-content .information-panel {
        padding: 14px;
    }
    .header-action-wrapp .icon {
        margin-right: 6px;
        font-weight: 900;
        cursor: pointer;
    }
    .header-action-wrapp .icon:last-child {
        margin-right: 0;
    }
    .header-action-wrapp .icon:hover {
        color: #555;
    }
    .modal-content .content {
        height: 100%;
    }
</style>