<script>
import {Inertia} from "@inertiajs/inertia";
import Pusher from "pusher-js";
import Echo from "laravel-echo";
import {capitalize} from "vue";

export default {
    props: {
        title: {
            type: String,
        },
        inboxes: {
            type: [Object, Array],
        },
        users: {
            type: [Object, Array],
        }
    },
    data: () => ({
        items: [],
        activeChat: null,
        msgContent: '',
    }),
    methods: {
        capitalize,
        /**
         * Take the incoming message, reconstruct session data and distribute
         */
        reconstructAndDistribute(message) {
            let messages = JSON.parse(sessionStorage.getItem(message.message.sender_id));
            messages.push(message.message);
            sessionStorage.setItem(message.message.sender_id, JSON.stringify(messages));
            if (this.activeChat?.inbox_id == message.message.sender_id) {
                this.activeChat.messages.push(message.message);
            } else {
                this.items.find(item => item.inbox_id === message.message.sender_id).messages.push(message.message);
            }
        },
        /**
         * Sets the messages and inboxes on frontend
         * @param data
         */
        setMessagesAndInboxes(data) {
            let items = [
                {
                    type: 'divider',
                }
            ];
            data.forEach((user) => {
                items.push({
                    inbox_id: user.id,
                    title: user.firstname,
                    prependAvatar: user.picture ? user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png",
                    subtitle: `<span class="text-primary">${user.last_message.sender_id == this.$page.props.auth.user.id ? 'You' : user.last_message.sender.firstname}</span> &mdash; ${user.last_message.content}`,
                    messages: user.messages,
                });
                items.push({
                    type: 'divider',
                });
                sessionStorage.setItem(user.id, JSON.stringify(user.messages.map(message => {
                    return {
                        sender_id: message.sender_id,
                        content: message.content,
                        receiver_id: message.receiver_id,
                        created_at: message.created_at
                    }
                })));
            });

            this.users.forEach((user) => {
                items.push({
                    inbox_id: user.id,
                    title: user.firstname,
                    prependAvatar: user.picture ? user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png",
                    subtitle: `<span class="text-primary">Inbox</span> &mdash; No messages yet`,
                    messages: [],
                });
                items.push({
                    type: 'divider',
                });

            });

            return items;
        },
        /**
         * Sets active chat when clicked on inbox
         */
        setActiveChat(chat) {
            this.activeChat = chat;
            let ref_id = chat.inbox_id;
        },
        /**
         * Send Message
         */
        sendMessage() {
            if (this.msgContent) {

                let text = this.msgContent;
                this.msgContent = '';
                this.activeChat.messages.push({
                    sender_id: this.$page.props.auth.user.id,
                    content: text,
                    receiver_id: this.activeChat.inbox_id,
                    created_at: new Date()
                });

                Inertia.post('/message', {
                    msg_content: text,
                    receiver_id: this.activeChat.inbox_id
                });
            }

        },
        /**
         * Scroll to the bottom of the div
         */
        scrollToBottom() {
            this.$refs.msgs.scrollTop = this.$refs.msgs.scrollHeight
        }
    },
    mounted() {
        this.items = this.setMessagesAndInboxes(this.inboxes);
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
            wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
            forceTLS: true ?? (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });

        window.Echo.private('message.' + this.$page.props.auth?.user.id).listen('.my-message', (message) => {
            this.reconstructAndDistribute(message);
        });

    },
    watch: {
        'activeChat.messages.length': function () {
            this.$nextTick(() => {
                this.scrollToBottom();
            })
        }
    }
}
</script>

<template>
    <Head>
        <title>{{title}}</title>
    </Head>
    <div class="inbox">
        <v-card
            class="mx-auto"
            max-width="400"
            width="400"
            title="Inbox"
        >
            <v-list :items="items"
                    item-props
            >
                <template v-for="item in items">
                    <v-list-item :ref="item.inbox_id" :key="item.inbox_id" v-if="item.title"
                                 :prepend-avatar="item.prependAvatar"
                                 :title="item.title"
                                 @click="setActiveChat(item)">
                        <v-list-item-content>
                            <div v-html="item.subtitle" class="msg-content"></div>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider v-if="item.type"></v-divider>
                </template>
            </v-list>

        </v-card>
        <div class="active-chat" v-if="activeChat">
            <div class="chat-header">
                <h3 style="margin: 0; text-align:center;">{{ activeChat.title }}</h3>
            </div>
            <div id="msgs" ref="msgs" class="chat-messages">
                <div class="message" v-for="message in activeChat.messages">
                    <div :class="message.sender_id == $page.props.auth.user.id ? 'sent' : 'received'">
                        <v-avatar v-if="message.sender_id != $page.props.auth.user.id"
                                  :image="activeChat.prependAvatar"></v-avatar>
                        <p :style="message.sender_id == $page.props.auth.user.id ? 'justify-self: end' : 'justify-self: start'"
                           class="text-message">
                            {{ capitalize(message.content) }}
                            <br>
                            <span style="color:grey;font-size: 14px;">{{
                                    this.$utils.normalDate(message?.created_at)
                                }}</span>
                        </p>

                        <v-avatar v-if="message.sender_id == $page.props.auth.user.id"
                                  :image="this.$page.props.auth.user.picture"></v-avatar>
                    </div>
                </div>
            </div>
            <div class="input-message">
                <v-text-field label="Message"
                              variant="outlined"
                              append-inner-icon="mdi-send"
                              hide-details="auto"
                              v-model="msgContent"
                              @click:append-inner="sendMessage()"
                ></v-text-field>
            </div>
        </div>
    </div>

</template>

<style scoped>
.inbox {
    display: grid;
    justify-content: start;
    height: calc(100% - 100px);
    grid-template-columns: max-content 3fr;
}

.inbox > .v-card {
    overflow-y: auto;
}

.msg-content {
    white-space: pre-wrap;
    word-break: break-word;
    text-overflow: ellipsis !important;
}

*:deep(.v-divider) {
    margin: 0 !important;
}

.v-list {
    padding: 0px;
}

.v-list:deep(.v-list-item__content) {
    max-height: 50px;
    white-space: pre-wrap;
    word-break: break-word;
    text-overflow: ellipsis !important;
}

.v-list .v-list-item {
    padding: 1em 0;
    max-height: 100px;
}


.active-chat {
    display: grid;
    height: 100%;
    overflow-y: auto;
    grid-template-rows:min-content 1fr min-content;
}

.chat-header {
    height: 52px;
    outline: 1px solid rgba(0, 0, 0, 0.12);
    display: grid;
    align-items: center;
}

.input-message {
    align-self: end;
}

.chat-messages {
    padding: 1em 0.5em;
    overflow-y: auto;
    display: grid;
    grid-auto-flow: row;
    grid-auto-rows: min-content;
    gap: 1em;
    height: 100%;
}

.message {
    display: grid;
}

.inbox:deep(.v-avatar) {
    height: 50px;
    width: 50px;
}

.sent {
    justify-self: end;
    display: grid;
    grid-auto-flow: column;
    column-gap: 1em;
    max-width: 45%;
}

.received {
    justify-self: start;
    display: grid;
    grid-auto-flow: column;
    column-gap: 1em;
    max-width: 45%;
}

.text-message {
    margin: 0;
    height: fit-content;
    align-self: center;
    background-color: rgba(16, 229, 197, 0.53);
    padding: 0.5em;
    border-radius: 7px;
    color: black;
    font-size: 1.1rem;
}
</style>
