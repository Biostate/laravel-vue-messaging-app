export default {
    namespaced: true,
    state: {
        user: 0
    },
    mutations: {
        set_user(state, user){
            state.user = user;
        }
    },

    getters: {

    },
    actions: {
        login({commit}, form){
            return axios.get('/sanctum/csrf-cookie').then( async csrf_response => {
                let data;
                await axios.post('/api/v1/login', form)
                    .then(response => {
                        data = response
                    })
                return data;
            }).catch(err => {
                console.log("Token alınamıyor", err);
            })
        },
        get_user_info({commit, state}){
            if(state.user == {})
                axios.get('/api/v1/user').then(response => {
                    commit('set_user', response.data)
                })
        }
    }
}
