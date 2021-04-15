export default {
	state: {
		clipboard: []
	},
	mutations: {
		setClipboard (state, clipboard) {
			state.clipboard = clipboard
		}
	},
	actions: {
		setClipboard ({commit}, data) {
			commit('setClipboard', data)
		},
		removeClipboard ({commit}) {
			commit('setClipboard', [])
		}
	},
	getters: {
		clipboard: s => s.clipboard
	}
  }
  