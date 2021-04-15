import Vue from 'vue'
import Vuex from 'vuex'

import loading from './loading'
import message from './message'
import files from './files'

Vue.use(Vuex)

export default new Vuex.Store({
	state: {
		domains: 'https://files.neworia.ru'
	},
	mutations: {
	},
	actions: {
	},
	getters: {
		domans: s => s.domains
	},
	modules: {
		loading,
		message,
		files
	}
})
