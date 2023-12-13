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
        fileName: '',
        file: null,
        url: '',
        _method: "put"
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
                        subtitle: `<span class="text-primary">${user.last_message.sender_id == this.$page.props.auth.user.id ? 'You' : user.last_message.sender.firstname}</span> &mdash; ${user.last_message.content.includes('/storage/') ? '<i style="color:darkcyan;">Media Format</i>' : user.last_message.content}`,
                        messages: user.messages,
                    });
                    items.push({
                        type: 'divider',
                    });
                    sessionStorage.getItem(user.id) ? sessionStorage.removeItem(user.id) : null;
                    sessionStorage.setItem(user.id, JSON.stringify(user.messages.map(message => {
                        return {
                            sender_id: message.sender_id,
                            content: message.content,
                            receiver_id: message.receiver_id,
                            created_at: message.created_at
                        }
                    })));
                }
            );

            this.users.forEach((user) => {
                items.push({
                    inbox_id: user.id,
                    title: user.firstname,
                    prependAvatar: user.picture ? user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png",
                    subtitle: `<span class = "text-primary" > Inbox </span> &mdash; No messages yet`,
                    messages: null,
                });
                items.push({
                    type: 'divider',
                });
                sessionStorage.getItem(user.id) ? sessionStorage.removeItem(user.id) : null;
            });

            return items;
        },
        /**
         * Sets active chat when clicked on inbox
         */
        setActiveChat(chat) {
            this.activeChat = chat;
            this.activeChat.messages = JSON.parse(sessionStorage.getItem(chat.inbox_id)) ? JSON.parse(sessionStorage.getItem(chat.inbox_id)) : [];
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
         * Submit form and send file
         */
        submit() {
            if (this.file) {
                console.log(this.file);
                let formData = new FormData();
                formData.append("file", this.file);
                formData.append("filename", this.fileName);
                formData.append("receiver_id", this.activeChat.inbox_id);
                formData.append("sender_id", this.$page.props.auth.user.id);

                let cnt = this.fileUrl;
                this.activeChat.messages.push({
                    sender_id: this.$page.props.auth.user.id,
                    content: cnt,
                    receiver_id: this.activeChat.inbox_id,
                    created_at: new Date()
                });

                Inertia.post('/message', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data', // Important: Set the content type
                    }
                });

            }
        },
        /**
         * Scroll to the bottom of the div
         */
        scrollToBottom() {
            this.$refs.msgs.scrollTop = this.$refs.msgs.scrollHeight
        },
        /**
         * Handle file inputs
         */
        handleInput(e) {
            const file = e.target.files[0];
            this.fileName = file.name;

            this.fileUrl = URL.createObjectURL(file);
            this.submit();

        },
        /**
         * Open a form to pick a file
         */
        selectFile() {
            this.$refs.fileInput.click();
        }
    },
    mounted() {
        this.items = this.setMessagesAndInboxes(this.inboxes);

        window.Echo.private('message.' + this.$page.props.auth?.user.id).listen('.my-message', (message) => {
            this.reconstructAndDistribute(message);
        });

    },
    watch: {
        'activeChat.messages.length':

            function () {
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
                            {{
                                !(message.content.includes('/storage/') || message.content.includes('blob:')) ? capitalize(message.content) : ''
                            }}
                            <img ref="image" style="height:170px;aspect-ratio: auto;"
                                 v-if="message.content.includes('/storage/') || message.content.includes('blob:')"
                                 :src="message.content" alt="IF IMAGE IS NOT SHOWING PLEASE RELOAD">
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
                              prepend-inner-icon="mdi-paperclip"
                              append-inner-icon="mdi-send"
                              hide-details="auto"
                              v-model="msgContent"
                              @click:append-inner="sendMessage()"
                              @click:prepend-inner="selectFile()"
                ></v-text-field>
                <form enctype="multipart/form-data" @submit.prevent="submit">
                    <input ref="fileInput" style="display: none;" type="file" @change="handleInput"
                           @input="this.file = $event.target.files[0]"
                           accept="image/jpg, image/jpeg, image/png"
                           class="file-input"/>

                </form>
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

.file-input:deep(.v-input__control) {
    display: none;
}

.file-input:deep(.v-input__prepend) {
    margin-left: auto;
    margin-right: auto;
}

.file-input {
    font-size: 1.5em;
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
    display: grid;
    padding: 0.5em;
    grid-template-columns:1fr min-content;
    gap: 0.5em;
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
