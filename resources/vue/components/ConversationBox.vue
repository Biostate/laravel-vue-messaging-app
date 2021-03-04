<template>
    <div :class="{selected: is_selected}" class="contact d-flex align-items-center py-2 px-3 mb-2 rounded-3" role="button" @click="$emit('selected', conversation.id)">
        <div class="left-border rounded-end"></div>
        <img src="/img/profile_pictures/default.jpg" class="rounded-circle" alt="Profile Photo">
        <div class="contact-middle mx-3">
            <div class="contact-name text-gray-3">
                {{ conversation.participants[0].user.name }}
            </div>
            <div class="contact-last-message text-muted" v-if="conversation.latest_message">
                {{ conversation.latest_message.message }}
            </div>
        </div>
        <div class="contact-left ms-auto">
            <div class="dropdown">

            </div>
            <div class="last-message-time" v-if="conversation.latest_message">
                {{ convert_date_time(conversation.latest_message.created_at) }}
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        is_selected: {type: Boolean, default: 0, required: false},
        conversation: {type: Object, default: null, required: true}
    },
    methods: {
        convert_date_time(date) {
            let year = new Date(date)
            if(this.isToday(year))
                return year.getHours()+':'+year.getMinutes()
            else
                return year.getFullYear()+'.'+year.getMonth()+'.'+year.getDate()
        },
        isToday(someDate){
            const today = new Date()
            return someDate.getDate() == today.getDate() &&
                someDate.getMonth() == today.getMonth() &&
                someDate.getFullYear() == today.getFullYear()
        }
    }
}
</script>
