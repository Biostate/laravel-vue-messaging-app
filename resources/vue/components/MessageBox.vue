<template>
        <div class="message rounded p-2 mt-2" :class="{'bg-primary text-white ml-auto bd-highlight': is_same_user, 'bg-muted': !is_same_user,}">
            <div class="message-time">
                {{message.created_at}}
            </div>
            <div class="message-inner">
                {{message.message}}
            </div>
        </div>
</template>


<script>
export default {
    props: {
        message: {type: Object, default: null, required: true},
        is_same_user: {type: Boolean, default: 0, required: true}
    }
}
</script>
