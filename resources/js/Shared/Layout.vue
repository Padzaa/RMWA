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
         * Retrieves the notifications from local storage.
         */
        getNotifications() {
            if(localStorage.getItem('recipeShared') || localStorage.getItem('publicRecipeCreated') || localStorage.getItem('recipeLiked')){
                return [
                    JSON.parse(localStorage.getItem('recipeShared')),
                    JSON.parse(localStorage.getItem('publicRecipeCreated')),
                    JSON.parse(localStorage.getItem('recipeLiked'))
                ];
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
            console.log(data);
            if(data.recipeShared) {
                localStorage.setItem('recipeShared', JSON.stringify(data.recipeShared));
            }
            if(data.publicRecipeCreated){
                localStorage.setItem('publicRecipeCreated', JSON.stringify(data.publicRecipeCreated));
            }
            if(data.recipeLiked){
                localStorage.setItem('recipeLiked', JSON.stringify(data.recipeLiked));
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
