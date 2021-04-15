export default {
  state: {
    isLoad: false
  },
  mutations: {
    isLoad (state, isLoad) {
        state.isLoad = isLoad
    }
  },
  actions: {
    onLoad ({commit}) {
        commit('isLoad', true)
    },
    onLoadClose ({commit}) {
        commit('isLoad', false)
    }
  },
  modules: {
  },
  getters: {
    isLoad: s => s.isLoad
  }
}
