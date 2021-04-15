import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'
import ElementUI from 'element-ui'
import vmodal from 'vue-js-modal'

import 'element-ui/lib/theme-chalk/index.css'
import 'vue-js-modal/dist/styles.css'

Vue.use(ElementUI)
Vue.use(vmodal)

Vue.prototype.$http = axios

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
