<script>
import Header from "./Header.vue";
import Alert from "./Alert.vue";
import TechnicalSupport from "./TechnicalSupport.vue";
import {Inertia} from "@inertiajs/inertia";

export default {
    components: {
        Alert,
        Header,
        TechnicalSupport
    },
    watch: {
        "$page.url": function (newUrl, oldUrl) {
            this.$page.url = newUrl;
        },
        not_key_len: function () {
            this.notifications = this.$utils.getNotifications();
        },
    },
    data() {
        return {
            notifications: this.$utils.getNotifications(),
            not_key_len: 1000,
            // technicalSupportRequests: JSON.parse(sessionStorage.getItem('technicalSupportRequests')) || []
        }
    },
    methods: {
        /**
         * Re-get props for technical support(pendingRequests as boolean and acceptedRequests as boolean)
         */
        getTSRProps() {
            Inertia.reload({
                preserveState: true,
                preserveScroll: true,
                only: ['pendingRequests', 'acceptedRequests'],
                onSuccess: () => {
                    this.$refs.technicalSupport.$data.requestedSupport = this.$page.props.pendingRequests || this.$page.props.acceptedRequests
                }
            });
        },
        /**
         * Set session storage and technical support data when request is denied
         */
        adaptDataWhenTSRDenied() {
            sessionStorage.removeItem('operator');
            sessionStorage.removeItem('activeSupportChat');
            this.$refs.technicalSupport.$data.operator = [];
            this.$refs.technicalSupport.$data.activeChatMessages = [];
        },
        /**
         * Adapt session storage and technical support data when request is accepted
         */
        adaptDataWhenTSRAccepted(data) {
            sessionStorage.setItem('operator', JSON.stringify(data));
            sessionStorage.setItem('activeSupportChat', JSON.stringify([]));
            this.$refs.technicalSupport.$data.operator = data;
            Inertia.remember(data, 'operator');
            this.$refs.technicalSupport.$data.activeSupportChat = data;
        },
        /**
         * Technical Support show 'rules'
         */
        showRules() {
            return (this.$page.url === '/'
                || this.$page.url.includes('/dashboard')
                || this.$page.url.includes('/recipe') && this.$page.props.auth)
        }
    },
    mounted() {

        window.Echo.private('notifications.' + this.$page.props.auth?.user.id).listen('.my-notifications', (data) => {
            if (data.data?.notificationsOnLogin) {
                sessionStorage.setItem('notifications', JSON.stringify(data.data.notificationsOnLogin));
                this.notifications = this.$utils.getNotifications();
            }
            if (data.recipeCommented) {
                this.notifications.push(data.recipeCommented);
                sessionStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if (data.recipeRated) {
                this.notifications.push(data.recipeRated);
                sessionStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if (data.userFollowed) {
                this.notifications.push(data.userFollowed);
                sessionStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if (data.recipeShared) {
                this.notifications.push(data.recipeShared);
                sessionStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if (data.publicRecipeCreated) {
                this.notifications.push(data.publicRecipeCreated);
                sessionStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if (data.recipeLiked) {
                this.notifications.push(data.recipeLiked);
                sessionStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if (data.technicalSupport) {
                sessionStorage.setItem('technicalSupportRequests', JSON.stringify(data.technicalSupport));
            }
            this.notifications = this.$utils.getNotifications();
        });
        if (!this.$page.props.administrator) {
            window.Echo.private('technical-support').listenForWhisper('rejected', (data) => {
                if (this.$page.props.auth.user.id == data.recipient_id) {
                    this.adaptDataWhenTSRDenied();
                    this.getTSRProps();
                }
            });
            window.Echo.private('technical-support').listenForWhisper('terminated', (data) => {
                if (this.$page.props.auth.user.id == data.recipient_id) {
                    this.adaptDataWhenTSRDenied();
                    this.getTSRProps();
                }
            });
            window.Echo.private('technical-support').listenForWhisper('accepted', (data) => {
                if (this.$page.props.auth.user.id == data.recipient_id) {
                    this.adaptDataWhenTSRAccepted(data.operator);
                    this.getTSRProps();
                    this.$refs.technicalSupport.activeSupportChat = this.$refs.technicalSupport.$data.operator;
                }
            });
            window.Echo.private('technical-support').listenForWhisper('processed', (data) => {
                if (this.$page.props.auth.user.id == data.recipient_id) {
                    this.adaptDataWhenTSRDenied();
                    this.getTSRProps();
                }
            });

        }
        if (this.$page.props.administrator) {
            window.Echo.channel('private-technical-support').listenForWhisper('logout', (data) => {
                sessionStorage.removeItem('technical-' + data.user_id);
            });
            window.Echo.private('technical-support').listen('.technical-support-request', (data) => {
                if (this.$page.props.auth.user.id == data.technicalSupport.notifiable_id) {
                    data.technicalSupport.id = data.id;
                    data.technicalSupport.data = JSON.parse(data.technicalSupport.data);
                    let existingTechnicalSupportRequests = this.$utils.getTechnicalSupportRequests();
                    existingTechnicalSupportRequests.push(data.technicalSupport);
                    existingTechnicalSupportRequests.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    sessionStorage.setItem('technicalSupportRequests', JSON.stringify(existingTechnicalSupportRequests));
                    this.$refs.technicalSupport.$data.technicalSupportRequests = this.$utils.getTechnicalSupportRequests();
                }
            });
        }
        window.Echo.private('technical-support').listenForWhisper('new-message', (data) => {
            if (this.$page.props.auth.user.id == data.recipient_id) {
                if (this.$page.props.administrator) {
                    this.$utils.updateSupportChatMessages(data, data.user_id);
                    this.$refs.technicalSupport.$data.activeChatMessages = this.$utils.getMessagesForSupportChat(data.user_id);
                } else {
                    this.$utils.updateSupportChatMessages(data);
                    this.$refs.technicalSupport.$data.activeChatMessages = this.$utils.getMessagesForSupportChat()
                }
            }
        });
    }
    ,

}
</script>

<template>
    <Header :pageUrl="this.$page.url" :notifications="this.notifications" :key="not_key_len"/>
    <slot ref="slot"></slot>
    <Alert ref="alertBox" v-if="this.$attrs.alertFlash || this.$attrs.errors.file"
           :alertFlash="this.$attrs.alertFlash || {
        message:this.$attrs.errors.file,
        type:'error',
        title:'Error',
    }"/>
    <TechnicalSupport ref="technicalSupport" v-if="showRules()"/>
    <!--    <v-btn class="previous-page" icon="mdi-reply" @click="$inertia.get('/back')"></v-btn>-->
</template>


<style scoped>


/*
.previous-page {
    position: fixed;
    bottom: 20px;
    left: 20px;
    font-size: 1.7em;
    height: 55px;
    width: 55px;
    background: orangered;
}*/
</style>
