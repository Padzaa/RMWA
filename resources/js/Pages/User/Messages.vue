<script>
import {Inertia} from "@inertiajs/inertia";
import {capitalize} from "vue";
import Alert from "../../Shared/Alert.vue";
import Inboxes from "../../Shared/Inboxes.vue"

export default {
    components: {Inboxes, Alert},
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
        _method: "put",
        shouldShow: false,
        errorMsg: {
            'title': 'Error',
            'message': 'File size too big',
            'type': 'error'
        },
        tooltip: true,
        isTyping: false,
        typingMessage: '',
        typingAvatar: null,
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
                    sessionStorage.getItem(user.id) && sessionStorage.removeItem(user.id);

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
                    subtitle: `<span class = "text-primary" >No messages yet </span>`,
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
                let inertia = this;
                Inertia.post('/message', {
                    msg_content: text,
                    receiver_id: this.activeChat.inbox_id
                }, {
                    preserveState: true,
                    onSuccess() {
                        inertia.activeChat.messages.push({
                            sender_id: inertia.$page.props.auth.user.id,
                            content: text,
                            receiver_id: inertia.activeChat.inbox_id,
                            created_at: new Date()
                        });
                        sessionStorage.setItem(inertia.activeChat.inbox_id, JSON.stringify(inertia.activeChat.messages));
                        inertia.updateInboxes();
                        inertia.startTyping();
                    }
                });


            }

        },
        /**
         * Submit form and send file
         */
        submit(chat) {
            if (this.file) {
                let formData = new FormData();
                formData.append("file", this.file);
                formData.append("filename", this.fileName);
                formData.append("receiver_id", this.activeChat.inbox_id);
                formData.append("sender_id", this.$page.props.auth.user.id);
                let item = chat;
                let cnt = this.fileUrl;
                let inertia = this;
                Inertia.post('/message', formData, {
                    preserveState: true,
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    onSuccess() {
                        inertia.setActiveChat(item);
                        inertia.activeChat.messages.push({
                            sender_id: inertia.$page.props.auth.user.id,
                            content: cnt,
                            receiver_id: inertia.activeChat.inbox_id,
                            created_at: new Date()
                        });
                        sessionStorage.setItem(inertia.activeChat.inbox_id, JSON.stringify(inertia.activeChat.messages));
                        inertia.updateInboxes();
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
         * Update inboxes
         */
        updateInboxes() {
            let inertia = this;
            Inertia.reload({
                only: ['inboxes', 'users'],
                onSuccess() {
                    inertia.items = inertia.setMessagesAndInboxes(inertia.inboxes);
                }
            });
        },
        /**
         * Handle file inputs
         */
        handleInput(item) {
            let file = event.target.files[0];

            if (file.size / 1000000 < 10) {
                this.fileName = file.name;
                this.fileUrl = URL.createObjectURL(file);
                console.log(this.fileName, this.fileUrl);
                this.submit(item);
            } else {
                this.shouldShow = true;
            }
            this.$refs.fileInput.value = null;
        },
        /**
         * Open a form to pick a file
         */
        selectFile() {
            this.$refs.fileInput.click();
        },
        /**
         * Check for upload big size error and refresh
         */
        checkForUploadError() {
            if (new URLSearchParams(window.location.search).get('error')) {
                this.shouldShow = true;
                setTimeout(() => {
                    Inertia.visit('/message');
                }, 1200);
            }
        },
        /**
         * If started typing send event isTyping
         */
        startTyping() {
            if (this.msgContent) {
                window.Echo.private('is_typing.' + this.activeChat.inbox_id).whisper('messageTyping', {
                    sender_picture: this.$page.props.auth.user.picture ? this.$page.props.auth.user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png",
                    sender_id: this.$page.props.auth.user.id,
                    message: this.$page.props.auth.user.firstname + ' is typing...'
                });
            } else {
                window.Echo.private('is_typing.' + this.activeChat.inbox_id).whisper('stoppedTyping', {});
            }
        },
        /**
         * Reset typing data
         */
        resetTypingData() {
            this.isTyping = false;
            this.typingMessage = '';
        },
    }
    ,
    mounted() {
        this.checkForUploadError();
        this.items = this.setMessagesAndInboxes(this.inboxes);

        window.Echo.private('message.' + this.$page.props.auth?.user.id).listen('.my-message', (message) => {
            this.reconstructAndDistribute(message);
            this.updateInboxes();
        });
    }
    ,
    watch: {
        'activeChat.messages.length':

            function () {
                this.$nextTick(() => {
                    this.scrollToBottom();
                })
            },
        'activeChat.inbox_id':
            function () {
                this.resetTypingData();
                this.startTyping();
                window.Echo.private('is_typing.' + this.$page.props.auth?.user.id).listenForWhisper('messageTyping', (e) => {
                    if (this.activeChat.inbox_id == e.sender_id) {
                        this.typingAvatar = e.sender_picture;
                        this.typingMessage = e.message;
                        this.isTyping = true;
                    }
                });
                window.Echo.private('is_typing.' + this.$page.props.auth?.user.id).listenForWhisper('stoppedTyping', () => {
                    this.resetTypingData();
                });
            },

    }
}
</script>

<template>
    <Head>
        <title>{{title}}</title>
    </Head>
    <div class="inbox">
        <Alert ref="alertBox" v-if="this.shouldShow" :alert-flash="errorMsg"/>
        <Inboxes :key="items" :items="items" :set-active-chat="this.setActiveChat"/>
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
                <div class="message" v-if="typingMessage && isTyping">
                    <div class="received">
                        <v-avatar
                            :image="this.typingAvatar"

                        ></v-avatar>
                        <p style="justify-self: start" class="text-message pulse">{{ typingMessage }}</p>
                    </div>
                </div>
            </div>
            <div class="input-message" style="position: relative">
                <form @submit.prevent="sendMessage">
                    <v-text-field label="Message"
                                  id="messageInput"
                                  variant="outlined"
                                  name="content"
                                  prepend-inner-icon="mdi-paperclip"
                                  append-inner-icon="mdi-send"
                                  messages="Max upload file size is 10MB"
                                  v-model="msgContent"
                                  @keyup="startTyping"
                                  @click:append-inner="sendMessage()"
                                  @click:prepend-inner="selectFile()"
                    ></v-text-field>
                </form>
                <form enctype="multipart/form-data" @submit.prevent="submit">
                    <input ref="fileInput" style="display: none;" type="file" @change="handleInput(this.activeChat)"
                           @input="this.file = $event.target.files[0]"
                           name="file"
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
    max-width: 50%;
}

.received {
    justify-self: start;
    display: grid;
    grid-auto-flow: column;
    column-gap: 1em;
    max-width: 50%;
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

@keyframes pulse {
    0% {
        opacity: 0.3;
    }
    30% {
        opacity: 0.4;
    }
    50% {
        opacity: 0.6;
    }
    80% {
        opacity: 0.8;
    }
    100% {
        opacity: 0.3;
    }
}

.pulse {
    animation-name: pulse;
    animation: pulse 1.5s infinite;
}

.v-text-field:deep(.v-input__details) {
    color: red;
    font-weight: bold;
}

.typing {
    bottom: 100%;
    left: 8px;
    background-color: rgba(16, 229, 197, 0.53);
    padding: 7px 10px;
    border-radius: 5px;
}
</style>
