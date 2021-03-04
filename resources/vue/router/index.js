const MessagingContainer = () => import(/* webpackChunkName: "MessagingContainer" */ '../views/MessagingContainer');
const Login = () => import(/* webpackChunkName: "Login" */ '../views/Login');

import VueRouter from 'vue-router'
const routes = [
    { path: '/', component: MessagingContainer, meta: {requiresAuth: true} },
    { path: '/login', component: Login, meta: {requiresAuth: false} }
]

const router = new VueRouter({
    mode: 'history',
    routes
})

import store from "../store";

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        axios.get('/api/v1/user/check').then(response => {
            next()
        }).catch(err => {
            console.log(err)
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            })
        })
    } else {
        next() // make sure to always call next()!
    }
})

export default router;
