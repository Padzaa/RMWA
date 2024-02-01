<script>
import {updateSupportChatMessages} from "../utils.js";
import {capitalize} from "vue";
import {Inertia} from "@inertiajs/inertia";

export default {
    data() {
        return {
            chatTsInbox: false,
            message: '',
            operator: JSON.parse(sessionStorage.getItem('operator')) || [],
            acceptedRequests: [],
            activeSupportChat: null,
            activeChatMessages: [],
            requestedSupport: this.$page.props.pendingRequests || this.$page.props.acceptedRequests,
            technicalSupportRequests: [],
        }
    },
    methods: {
        capitalize,
        /**
         *
         */
        requestTechnicalSupport() {
            Inertia.put('/request-technical-support', {
                user_id: this.$page.props.auth.user.id
            });
            this.requestedSupport = true;
        },
        /**
         * Reset active support chat
         */
        resetActiveSupportChat() {
            this.activeSupportChat = null;
        },
        /**
         * Sets active support chat
         * @param inbox
         */
        setActiveSupportChat(inbox) {
            console.log(inbox)
            this.activeSupportChat = inbox;
            this.activeChatMessages = this.$utils.getMessagesForSupportChat(inbox.id);
        },
        /**
         * Send message to technical support
         */
        sendMessage() {
            let data = {
                user_id: this.$page.props.auth.user.id,
                message: this.message,
                sent_at: new Date(),
                recipient_id: this.activeSupportChat.id
            };
            if (!this.$page.props.administrator) {
                window.Echo.private('technical-support').whisper('new-message', data);
                this.$utils.updateSupportChatMessages(data);
                this.activeChatMessages = this.$utils.getMessagesForSupportChat();
            } else {
                window.Echo.private('technical-support').whisper('new-message', data);
                this.$utils.updateSupportChatMessages(data, this.activeSupportChat.id);
                this.activeChatMessages = this.$utils.getMessagesForSupportChat(this.activeSupportChat.id);
            }
            this.message = '';
        },
        /**
         * Accept or close(successfully) technical request
         */
        acceptOrCloseSuccessfullyTSR(id, user_id, currentStatus) {
            this.$utils.acceptTechnicalSupportRequest(id, currentStatus);
            Inertia.put('/update-tsrs/', {
                user_id: user_id,
                tsr_status: currentStatus === null ? 'accepted' : 'processed',
            }, {
                onSuccess: () => {
                    if (currentStatus === 'accepted') {
                        this.resetActiveSupportChat();
                        let existingRequests = this.$utils.getTechnicalSupportRequests();
                        existingRequests = existingRequests.filter(tsr => tsr.id !== id);
                        sessionStorage.setItem('technicalSupportRequests', JSON.stringify(existingRequests));
                    }
                    window.Echo.private('technical-support').whisper(currentStatus === null ? 'accepted' : 'processed', {
                        recipient_id: user_id,
                        operator: currentStatus === null ? this.$page.props.auth.user : null
                    });
                    window.Echo.private('technical-support').whisper('action', {});
                    sessionStorage.removeItem('technical-' + user_id);
                    this.technicalSupportRequests = this.$utils.getTechnicalSupportRequests();
                }
            });
        },
        /**
         * Reject or terminate technical request
         */
        rejectOrTerminateTSR(id, user_id, currentStatus) {
            this.$utils.declineTechnicalSupportRequest(id, currentStatus);
            Inertia.put('/update-tsrs/', {
                user_id: user_id,
                tsr_status: currentStatus === null ? 'rejected' : 'terminated'
            }, {
                onSuccess: () => {
                    if (currentStatus === 'accepted') {
                        this.resetActiveSupportChat();
                    }
                    window.Echo.private('technical-support').whisper(currentStatus === null ? 'rejected' : 'terminated', {
                        recipient_id: user_id
                    });
                    window.Echo.private('technical-support').whisper('action', {});
                    sessionStorage.removeItem('technical-' + id);
                    this.technicalSupportRequests = this.$utils.getTechnicalSupportRequests();
                }
            });
        },
        /**
         * Scroll to the bottom of the div
         */
        scrollToBottom() {
            if (this.$refs.msgs) {
                this.$refs.msgs.scrollTop = this.$refs.msgs.scrollHeight
            }
        }
    },
    mounted() {
        this.technicalSupportRequests = this.$page.props.technicalSupportRequests;
        sessionStorage.setItem('technicalSupportRequests', JSON.stringify(this.technicalSupportRequests));
        window.Echo.private('technical-support');
        this.operator = this.$page.props.currentOperator;
    },
    watch: {
        'activeChatMessages.length':
            function () {
                this.$nextTick(() => {
                    this.scrollToBottom();
                })
            },
    }
}
</script>

<template>
    <v-btn class="chatts" icon="mdi-chat" @click="[chatTsInbox = !chatTsInbox,this.resetActiveSupportChat(),
        !this.$page.props.administrator ? [this.activeChatMessages = this.$utils.getMessagesForSupportChat(),this.activeSupportChat = operator] : '',
    ]"></v-btn>

    <v-card v-if="chatTsInbox"
            class="chatts-inbox"
    >
        <v-card-title
            style="display: grid;grid-template-columns:1fr min-content;grid-auto-flow: column;border-bottom: 1px solid lightgray;">
            <span style="text-overflow: ellipsis;width:calc(100% - 10px);overflow: hidden;font-size: 1rem">{{
                    activeSupportChat && this.$page.props.administrator ? activeSupportChat.firstname + ' | ' + this.$utils.normalDate(activeSupportChat.created_at) : 'Technical Support'
                }}</span>
            <v-icon
                @click="this.$page.props.administrator && activeSupportChat ? this.resetActiveSupportChat() : chatTsInbox = !chatTsInbox">
                mdi-close
            </v-icon>
        </v-card-title>
        <div v-if="this.$page.props.administrator && !activeSupportChat"
             class="acceptedRequests">
            <span class="text-center font-italic text-grey-darken-2 mt-1" v-if="!technicalSupportRequests.length">No pending/accepted technical support requests<hr
                style="margin: 5px 0 0 0;"/></span>
            <v-list v-if="technicalSupportRequests.length"
                    :items="technicalSupportRequests"
                    item-props>
                <template v-for="request in technicalSupportRequests">
                    <v-list-item
                        :title="request.data.user.firstname"
                        :subtitle="request.data.user.email"
                        v-if="request.tsr_status !== 'rejected' && request.tsr_status !== 'terminated'"
                        @click="request.tsr_status === 'accepted' ? setActiveSupportChat(request.data.user) : null"
                    >
                        <template v-slot:append>
                            <v-icon class="chat-appended-icons text-green" v-if="request.tsr_status === null"
                                    @click="acceptOrCloseSuccessfullyTSR(request.id, request.data.user.id, request.tsr_status)">
                                mdi-check
                            </v-icon>
                            <span v-if="request.tsr_status === null"> &nbsp</span>
                            <v-icon class="chat-appended-icons text-red" v-if="request.tsr_status === null"
                                    @click="rejectOrTerminateTSR(request.id, request.data.user.id, request.tsr_status)">
                                mdi-close
                            </v-icon>
                            <v-icon class="chat-appended-icons text-green"
                                    v-if="request.tsr_status === 'accepted'"
                                    @click="acceptOrCloseSuccessfullyTSR(request.id, request.data.user.id, request.tsr_status)">
                                mdi-checkbox-marked-circle-auto-outline
                            </v-icon>
                            <span v-if="request.tsr_status === 'accepted'"> &nbsp</span>
                            <v-icon class="chat-appended-icons text-red" v-if="request.tsr_status === 'accepted'"
                                    @click="rejectOrTerminateTSR(request.id, request.data.user.id, request.tsr_status)">
                                mdi-progress-close
                            </v-icon>
                        </template>
                    </v-list-item>
                </template>
            </v-list>
        </div>
        <div class="activeSupportChat" style="overflow-y: hidden"
             v-if="this.$page.props.administrator ? activeSupportChat : activeChatMessages.length || operator.length">
            <div class="active-support-chat-header" v-if="this.$page.props.administrator">
                <span
                    class="text-center mt-1 text-grey-darken-2">{{
                        activeSupportChat?.email
                    }}</span>
            </div>
            <div class="active-support-chat-messages"
                 :class="!this.$page.props.administrator ? 'active-support-chat-messages-user-height' : ''"
                 ref="msgs"
                 style="display:grid;overflow-y: scroll;height:calc(100% - 30px); padding:1em">
                <div class="message" v-for="message in activeChatMessages">
                    <div :class="message.user_id == this.$page.props.auth.user.id ? 'sent' : 'received'">
                        <Link :href="'/user/'+ message.user_id">
                            <v-avatar v-if="message.user_id != this.$page.props.auth.user.id"
                                      :image="activeSupportChat.picture"
                            ></v-avatar>
                        </Link>

                        <p :style="message.user_id == this.$page.props.auth.user.id ? 'justify-self: end' : 'justify-self: start'"
                           class="text-message">
                            {{
                                !(message.message.includes('/storage/') || message.message.includes('blob:')) ? capitalize(message.message) : ''
                            }}
                            <br>
                            <span style="color:grey;font-size: 14px;">{{
                                    this.$utils.normalDate(message.sent_at)
                                }}</span>
                        </p>

                        <v-avatar v-if="message.user_id == this.$page.props.auth.user.id"
                                  :image="this.$page.props.auth.user.picture"></v-avatar>
                    </div>
                </div>
            </div>
        </div>
        <span class="text-center font-italic text-grey-darken-2 mt-1"
              v-if="!this.$page.props.administrator && this.$page.props.acceptedRequests && this.activeChatMessages.length === 0">Operator's name is {{
                operator.firstname
            }}.<br> Happy to assist you.</span>
        <div class="chatts-inbox-content" v-if="!this.$page.props.administrator">
            <div class="request-tech-supp"
                 v-if="!requestedSupport && this.$page.props.canRequestTechnicalSupport && !this.$page.props.pendingRequests">
            <span
                class="text-center font-italic text-grey-darken-2 mt-1">Request technical support and someone will join as soon as possible.</span>
                <v-btn color="primary" style="justify-self: center" @click="requestTechnicalSupport()">
                    Request Technical Support
                </v-btn>
            </div>
            <div class="request-tech-supp-pending"
                 v-if="requestedSupport && !this.$page.props.acceptedRequests">
            <span style="grid-row: 2"
                  class="text-center font-italic text-grey-darken-2 mt-1">
                Technical support request has been sent.
                <br>
                Operator will contact you shortly.</span>
            </div>
        </div>
        <form
            v-if="this.$page.props.administrator ? this.activeSupportChat : this.$page.props.acceptedRequests"
            @submit.prevent="sendMessage()">
            <v-card-actions>

                <v-text-field label="Message"
                              v-model="message"
                              append-inner-icon="mdi-send"
                              @click:append-inner="sendMessage()"
                              hide-details
                              required
                >
                </v-text-field>

            </v-card-actions>
        </form>
    </v-card>
</template>

<style scoped>
.chat-appended-icons {
    font-size: 2em;
}

.chatts {
    background: #8d158d;
    color: white;
    height: 75px;
    width: 75px;
    font-size: 25px;
    position: fixed;
    bottom: 20px;
    right: 15px;
    z-index: 9999;
    box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;

}

.chatts-inbox {
    position: fixed;
    width: 300px;
    height: 400px;
    right: 110px;
    bottom: 0;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px;
    display: grid;
    grid-template-rows: min-content 1fr min-content;
}

.chatts-inbox-content {
    height: 100%;
    overflow-y: auto;
    display: grid;
    grid-auto-flow: row;
    grid-auto-rows: min-content;
    gap: 1em;
}

.message {
    display: grid;
}

.text-message {
    margin: 0;
    height: fit-content;
    align-self: center;
    background-color: rgb(255, 255, 255);
    padding: 0.5em;
    border-radius: 7px;
    color: black;
    font-size: 1.1rem;
    word-break: break-all;
    box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
}

.active-support-chat-messages {
    overflow-y: scroll;
    display: grid;
    grid-auto-flow: row;
    grid-auto-rows: min-content;
    gap: 1em;
}

.active-support-chat-messages-user-height {
    height: calc(100% - 0px) !important;
}

.request-tech-supp {
    display: grid;
    grid-template-rows: min-content min-content 1fr;
    row-gap: 0.7em;
}

.request-tech-supp-pending {
    display: grid;
    grid-template-rows: repeat(3, 1fr);
    height: 100%;
}

.acceptedRequests {
    display: grid;
}

.active-support-chat-header {
    display: grid;
    height: 30px;
    border-bottom: 1px solid lightgray;
}

.sent {
    justify-self: end;
    display: grid;
    grid-auto-flow: column;
    column-gap: 1em;

}

.received {
    justify-self: start;
    display: grid;
    grid-auto-flow: column;
    column-gap: 1em;
}

</style>
