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
        },
        not_key_len: function () {
            this.notifications = this.$utils.getNotifications();
        }
    },
    data() {
        return {
            notifications: this.$utils.getNotifications(),
            not_key_len: 1000,
        }
    },
    methods: {},
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
            this.notifications = this.$utils.getNotifications();
        });

    },

}
</script>

<template>
    <Header :pageUrl="this.$page.url" :notifications="this.notifications" :key="not_key_len"/>
    <slot></slot>
    <Alert ref="alertBox" v-if="this.$attrs.alertFlash || this.$attrs.errors.file"
           :alertFlash="this.$attrs.alertFlash || {
        message:this.$attrs.errors.file,
        type:'error',
        title:'Error',
    }"/>
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
