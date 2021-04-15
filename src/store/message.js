export default {
    state: {
		message: '',
		isSuccess: false,
		isErrors: false
    },
	mutations: {
		sendSuccess (state, message) {
			state.message = message
			state.isSuccess = true
		},
		sendErrors (state, message) {
			state.message = message
			state.isErrors = true
		},
		closeSuccess (state) {
			state.message = ''
			state.isSuccess = false
		},
		closeClose (state) {
			state.message = ''
			state.isErrors = false
		}
	},
	actions: {
		sendSuccess ({commit}, message) {
			commit('sendSuccess', message)
		},
		sendErrors ({commit}, message) {
			commit('sendErrors', message)
		},
		closeSuccess ({commit}) {
			commit('closeSuccess')
		},
		closeErrors ({commit}) {
			commit('closeErrors')
		}
	},
	modules: {
	},
	getters: {
		isLoad: s => s.isLoad
	}
  }
  