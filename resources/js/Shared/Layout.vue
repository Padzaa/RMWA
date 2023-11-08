<script>
import Header from "./Header.vue";
import Alert from "./Alert.vue";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export default {
    components: {
        Alert,
        Header
    },
    watch: {
        "$page.url": function (newUrl, oldUrl) {
            this.$page.url = newUrl;
        }
    },
    data() {
        return {
            notifications: this.getNotifications(),
        }
    },
    methods: {
        /**
         * Retrieves the notifications from local storage and sort them by date.
         */
        getNotifications() {
            if(localStorage.getItem('notifications')){
                let notifications = JSON.parse(localStorage.getItem('notifications'));
                return notifications.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            }else{
                return [];
            }

        }
    },
    mounted() {
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

        window.Echo.private('notifications.' + this.$page.props.auth.user.id).listen('.my-notifications', (data) => {
            if(data.data?.notificationsOnLogin){
                localStorage.setItem('notifications', JSON.stringify(data.data.notificationsOnLogin));
                this.notifications = this.getNotifications();
            }
            if(data.userFollowed) {
                this.notifications.push(data.userFollowed);
                localStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if(data.recipeShared) {
                this.notifications.push(data.recipeShared);
                localStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if(data.publicRecipeCreated){
                this.notifications.push(data.publicRecipeCreated);
                localStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            if(data.recipeLiked){
                this.notifications.push(data.recipeLiked);
                localStorage.setItem('notifications', JSON.stringify(this.notifications));
            }
            this.notifications = this.getNotifications();
        });

    },

}
</script>

<template>
    <Header :pageUrl="this.$page.url" :notifications="this.notifications"/>
    <slot></slot>
    <Alert v-if="this.$attrs.alertFlash" :alertFlash="this.$attrs.alertFlash"/>
</template>


<style scoped>

</style>
