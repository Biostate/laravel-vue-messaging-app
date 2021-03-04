:
<template>
    <div class="container" id="app-container">
        <div class="row h-100 no-gutters" id="app-container-row">
            <div class="col-lg-3 col-md-4 col-sm-5 bg-light contact-box d-flex flex-column h-100">
                <div class="input-group flex-nowrap mt-3 px-3">
                    <span class="input-group-text bg-gray-2 border-0">
                        <b-icon icon="search"></b-icon>
                    </span>
                    <input v-model="search_query" type="text" class="form-control border-0 bg-gray-2" placeholder="Ara"
                           aria-label="Username" aria-describedby="addon-wrapping">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" v-b-modal.friend_request_modal>
                            <b-icon icon="person-plus-fill"></b-icon>
                        </button>
                    </div>
                </div>
                <b-tabs v-model="tab_index" content-class="" class="mt-3 overflow-auto align-self-stretch h-100"
                        align="center" small @input="input">
                    <b-tab active>
                        <template #title>
                            <b-icon icon="chat-square-fill"></b-icon>
                        </template>
                        <div class="contacts mt-3 px-3 position-relative" v-if="conversations.length > 0">
                            <conversation-box v-for="conversation in conversations" :conversation="conversation"
                                              :is_selected="selected_conversation == conversation.id"
                                              @selected="change_selected_conversation_id" v-bind:key="conversation.id"/>
                        </div>
                        <div v-else>
                            There is no Conversation
                        </div>
                    </b-tab>
                    <b-tab>
                        <template #title>
                            <b-icon icon="people-fill"></b-icon> {{user.friendship_requests_count}}
                        </template>
                        <h4 class="mx-2">Requests ({{user.friendship_requests_count}})</h4>
                        <div class="contacts mt-2 px-2 position-relative" v-if="friendship_requests.length > 0">
                            <friendship-request-box v-for="friendship_request in friendship_requests"
                                                    :friendship_request="friendship_request"
                                                    @status_changed="frienship_request_changed"
                                                    v-bind:key="friendship_requests.id"/>
                        </div>
                        <div v-else>
                            There is no request
                        </div>
                    </b-tab>
                </b-tabs>
                <div class="profile p-3 border-top mt-auto bd-highlight">
                    {{ user.name }} - <span role="button" @click="logout">ÇIKIŞ</span>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-7 bg-white message-box px-3 h-100">
                <div v-if="current_conversation" class="d-flex flex-column  h-100">
                    <div class="contact-name py-3">
                        {{ current_conversation.participants[0].user.typing ? "typing..." : "" }} {{ current_conversation.participants[0].user.name }}
                        <b-icon icon="circle-fill" :style="{color: current_conversation.online ? '#4cd137' : '#e84118'}"></b-icon>
                    </div>
                    <div class="seperator">
                        <img src="/svg/seperator.svg" class="img-fluid" alt="">
                    </div>
                    <div
                        class="contact-messages align-items-start d-flex py-2  overflow-auto flex-column-reverse h-100">
                        <message-box v-for="message in messages" :message="message"
                                     :is_same_user="message.user_id == user.id" v-bind:key="message.id"/>
                        <div class="text-center w-100" v-if="messages.length <= 0">There is no message</div>
                    </div>
                    <div class="send-message-container border-top mt-auto bd-highlight mb-3 pt-3">
                        <div class="input-group">
                            <input v-model="message" type="text" class="form-control border-0 bg-gray-2"
                                   aria-label="Dollar amount (with dot and two decimal places)"
                                   @keydown.enter="send_message">
                            <span class="input-group-text border-0 bg-gray-2">&DoubleRightArrow;</span>
                            <span class="input-group-text border-0 bg-gray-2">G</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center">Let's get messaging!</div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="friend_request_modal" tabindex="-1" aria-labelledby="friend_request_modal"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>


        <b-modal id="friend_request_modal" title="Add New Contact" @ok="add_contact" @hidden="add_contact_hidden"
                 ok-title="Add Contact">
            <b-form-group
                id="friend_request_email_group"
                description="Please write email address that you want to add."
                label="Email Address:"
                label-for="friend_request_email"
                valid-feedback="Your request sent!"
                :invalid-feedback="friend_request_email_invalid_feedback"
                :state="friend_request_email_group_state"
            >
                <b-form-input id="friend_request_email" v-model="friend_request_email"
                              :state="friend_request_email_state" type="email" trim></b-form-input>
            </b-form-group>
        </b-modal>
    </div>
</template>


<script>
import {mapState} from 'vuex';
import {Howl} from 'howler';

export default {
    data() {
        return {
            user: {},
            selected_conversation: null,
            current_conversation: null,
            search_query: null,
            friendship_requests: [],
            conversations: [],
            messages: [],
            message: null,
            friend_request_email: null,
            friend_request_email_state: null,
            friend_request_email_invalid_feedback: null,
            friend_request_email_group_state: null,
            sound: new Howl({
                src: ['/sound/notification.mp3']
            }),
            tab_index: 0
        }
    },
    computed: {
//       ...mapState('user',{
//           user: state => state.user
// })
    },
    beforeRouteEnter(to, from, next) {
        let user = axios.get('/api/v1/user');
        let conversations = axios.get('/api/v1/user/conversations');
        axios.all([user, conversations]).then(axios.spread((...responses) => {
            next(vm => {
                vm.user = responses[0].data
                vm.conversations = responses[1].data
            })
        })).catch(errors => {
            // react on errors.
        })
    },

    methods: {
        change_selected_conversation_id(id) {
            try {
                if(this.current_conversation)
                window.Echo.leave(`conversation-${this.current_conversation.id}`);
                window.Echo.join(`conversation-${id}`)
                    .here((users) => {
                        if (users.length >= 2) {
                            this.$set(this.conversations[this.conversations.findIndex(v => v.id == id)], 'online', true)
                        }
                    })
                    .joining((user) => {
                        this.$set(this.conversations[this.conversations.findIndex(v => v.id == id)], 'online', true)
                    })
                    .leaving((user) => {
                        this.$set(this.conversations[this.conversations.findIndex(v => v.id == id)], 'online', false)
                    }).listenForWhisper('typing', (e) => {
                        this.$set(this.current_conversation.participants[0].user,'typing',e)
                    });
            } catch (e) {
                this.selected_conversation = null
                this.current_conversation = null
            }

            this.selected_conversation = id
            this.current_conversation = this.conversations.find(v => {
                return v.id == id
            })
            axios.get(`/api/v1/user/conversations/${this.current_conversation.id}/messages`).then(response => {
                this.messages = response.data
            })
        },
        send_message() {
            axios.post(`/api/v1/user/conversations/${this.current_conversation.id}/messages`, {message: this.message}).then(response => {
                this.messages.unshift(response.data)
                this.message = "";
                let conv_index = this.conversations.findIndex(v => v.id == this.current_conversation.id)
                this.$set(this.conversations[conv_index], 'latest_message', response.data)
                this.change_conversation_index_to_zero(conv_index)
            })
        },
        logout() {
            axios.post('/api/v1/logout').then(res => {
                this.$router.push('/login')
            })
        },
        change_conversation_index_to_zero(conv_index) {
            this.array_move(this.conversations, conv_index, 0)
        },
        array_move(arr, old_index, new_index) {
            if (old_index == 0)
                return arr
            if (new_index >= arr.length) {
                var k = new_index - arr.length + 1;
                while (k--) {
                    arr.push(undefined);
                }
            }
            arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
            return arr; // for testing
        },
        add_contact(e) {
            e.preventDefault()
            axios.post('/api/v1/user/friendship_requests', {
                email: this.friend_request_email
            }).then(response => {
                this.friend_request_email_state = response.data.result
                this.friend_request_email_group_state = response.data.result
                this.friend_request_email_invalid_feedback = response.data.message
            }).catch(err => {
                this.friend_request_email_state = false
                this.friend_request_email_group_state = false
                if (err.response.status == 404)
                    this.friend_request_email_invalid_feedback = "User doesn't exist. Please be sure you entered email address correct."
                else
                    this.friend_request_email_invalid_feedback = "There is a problem about server. Please contact your administrator."
            })
        },
        add_contact_hidden() {
            this.friend_request_email_state = null
            this.friend_request_email_group_state = null
            this.friend_request_email_invalid_feedback = null
            this.friend_request_email = null
        },
        input(a) {
            if(a == 1) {
                this.friendship_requests == []
                axios.get('/api/v1/user/friendship_requests').then(response => {
                    this.friendship_requests = response.data.data;
                })
            }
        },
        frienship_request_changed(args) {
            let fr_index = this.friendship_requests.findIndex(x => x.id == args.id)
            if (fr_index != -1) {
                this.user.friendship_requests_count--
                this.$set(this.friendship_requests[fr_index], 'status', (args.status ? "Accepted" : "Rejected"))
            }
        }
    },
    mounted() {
        window.Echo.join(`user-${this.user.id}`)
            .listen('.NewMessage', (e) => {
                if (document.hidden || e.message.conversation_id != (this.current_conversation ? this.current_conversation.id : null)) {
                    this.sound.play();
                }
                if (this.current_conversation)
                    if (e.message.conversation_id == this.current_conversation.id)
                        this.messages.unshift(e.message)
                let conv_index = this.conversations.findIndex(v => v.id == e.message.conversation_id)
                this.$set(this.conversations[conv_index], 'latest_message', e.message)
                this.change_conversation_index_to_zero(conv_index)
            });

        window.Echo.join(`user-${this.user.id}`)
            .listen('.NewFriendshipRequest', (e) => {
                this.$bvToast.toast(`There is a new contact request from ${e.from_user.name}!`, {
                    title: `New contact request!`,
                    variant: "success",
                    solid: true
                })
                if (this.tab_index == 1)
                    this.friendship_requests.unshift(e);
            });

        window.Echo.join(`user-${this.user.id}`)
            .listen('.FriendshipRequestAccepted', (e) => {
                this.user.friendship_requests_count++
                this.conversations.unshift(e.conversation)
            });
    },
    watch: {
        friend_request_email() {
            this.friend_request_email_state = null
            this.friend_request_email_group_state = null
            this.friend_request_email_invalid_feedback = null
        },
        message(newValue){
            if(this.current_conversation)
                window.Echo.join(`conversation-${this.current_conversation.id}`).whisper('typing', newValue != "" ? true : false);
        }
    }
}
</script>
