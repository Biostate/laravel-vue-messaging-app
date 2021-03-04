<template>
    <div class="contact d-flex align-items-center py-2 px-2 mb-2 rounded-3">
        <div class="left-border rounded-end"></div>
        <img src="/img/profile_pictures/default.jpg" class="rounded-circle" alt="Profile Photo">
        <div class="contact-middle mx-3">
            <div class="contact-name text-gray-3">
                {{ friendship_request.from_user.name }}
            </div>
        </div>
        <div class="contact-left ms-auto d-flex">
            <template v-if="!friendship_request.status">
                <b-button @click="update_friendship_request(true)" size="sm" variant="primary"><b-icon icon="check"></b-icon></b-button>
                <b-button @click="update_friendship_request(false)" size="sm" variant="danger"><b-icon icon="x"></b-icon></b-button>
            </template>
            <template v-else>{{ friendship_request.status }}</template>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        friendship_request: {type: Object, default: null, required: true}
    },
    methods: {
        update_friendship_request(is_user_accepted){
            axios.patch('/api/v1/user/friendship_requests/'+this.friendship_request.id, {
                is_user_accepted: is_user_accepted
            }).then(response => {
                this.$emit('status_changed', {id: this.friendship_request.id, status: is_user_accepted})
            })
        }
    }
}
</script>
