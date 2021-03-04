import Vuex from 'vuex'
import Vue from 'vue'
import user from './modules/user'
Vue.use(Vuex)
const store = new Vuex.Store({
    state: {
        count: 0
    },
    mutations: {
        increment (state) {
            state.count++
        }
    },
    modules: {
        user
    }
})

export default store;
