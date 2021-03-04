<template>
    <main class="form-signin">
        <form v-on:submit.prevent="onSubmit">
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
            <label for="inputEmail" class="visually-hidden">Email address</label>
            <input v-model="form.email" type="email" id="inputEmail" class="form-control" placeholder="Email address" required="" autofocus="">
            <label for="inputPassword" class="visually-hidden">Password</label>
            <input v-model="form.password" type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
        </form>
    </main>
</template>

<script>
import {mapActions} from 'vuex'
export default {
    data() {
        return {
            form: {
                email: null,
                password: null
            }
        }
    },

    methods: {
        ...mapActions('user', [
            'login'
        ]),
        onSubmit(){
            this.login(this.form).then(response => {
                if(response.data.result)
                    this.$router.push('/')
            })
        }
    },
}
</script>

<style>
.form-signin{
    width: 100%;
    max-width: 330px;
    padding: 15px;
    margin: auto;
}
</style>
