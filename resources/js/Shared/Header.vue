<template>

    <header>
        <Link href="/" class="logo-holder">
            <img src="https://png.pngtree.com/png-vector/20220709/ourmid/pngtree-hat-chef-logo-png-image_5820915.png"
                 alt="logo" class="logo">
        </Link>
        <div class="menu">
            <button v-if="this.$page.props.auth" @click="opener = !opener" class="opener">
                <img src="../../../public/menu.svg" alt="menu">
            </button>
            <v-app v-if="this.$page.props.auth" style="position:absolute;">

                <v-navigation-drawer :key="$props.pageUrl"
                                     style="top:0;width:320px;background-color: rgb(43,43,43);border: none;z-index: 9999;"
                                     v-model="opener"
                                     temporary
                                     class="side-menu-drawer"

                >
                    <div class="space">
                        <button @click="opener = !opener" class="opener">
                            <img src="../../../public/close.svg" alt="menu">
                        </button>
                    </div>
                    <v-divider style="color:white;margin: 0;"></v-divider>
                    <div style="display: grid;">
                        <Link @click="opener = !opener" style="display:grid;"
                              :href="'/user/'+ this.$page.props.auth.user.id +'/edit'">
                            <v-list-item value="user" class="side-user"
                                         style="padding: 15px 0 15px 1em;"
                                         :prepend-avatar="pic"

                            >{{ this.$page.props.auth.user.firstname + " " + this.$page.props.auth.user.lastname }}

                            </v-list-item>

                        </Link>
                        <v-divider style="color:white;margin: 0;"></v-divider>
                        <div style="display: grid;padding: 10px 0;">
                            <Link href="/logout" as="button" method="POST" style="justify-self:center;width:90%;">
                                <v-btn class="logout-btn" style="justify-self:center;font-size: 1em;width:100%;"
                                       variant="outlined" color="white"
                                       append-icon="mdi-logout">Logout
                                </v-btn>
                            </Link>
                        </div>

                    </div>


                    <v-divider style="color:white;;margin: 0;"></v-divider>

                    <v-expansion-panels style="border-radius: 0" variant="accordion" v-model="active">

                        <Link as="button" href="/"
                              style="background-color: #494949;font-size:1.25rem;border-radius: 0;height: 70px;width:100%;font-weight: 550;text-align: start;padding: 16px 24px;"
                              @click="opener = !opener"
                              :class="this.$page.url == '/' ? 'active' : ''"
                              class="text-white home-btn"
                        >Home
                        </Link>
                        <Link as="button" href="/guest/public"
                              style="background-color: #494949;font-size:1.25rem;border-radius: 0;height: 70px;width:100%;font-weight: 550;text-align: start;padding: 16px 24px;"
                              @click="opener = !opener"
                              :class="this.$page.url.includes('/public') ? 'active' : ''"
                              class="text-white home-btn"
                        >Public
                        </Link>
                        <Link as="button" href="/message"
                              style="background-color: #494949;font-size:1.25rem;border-radius: 0;height: 70px;width:100%;font-weight: 550;text-align: start;padding: 16px 24px;"
                              @click="opener = !opener"
                              :class="this.$page.url == '/message' ? 'active' : ''"
                              class="text-white home-btn"
                        >Chat
                        </Link>
                        <Link v-if="this.$page.props.auth.user.is_admin == 1" as="button" href="/dashboard"
                              style="background-color: #494949;font-size:1.25rem;border-radius: 0;height: 70px;width:100%;font-weight: 550;text-align: start;padding: 16px 24px;"
                              @click="opener = !opener"
                              :class="this.$page.url == '/dashboard' ? 'active' : ''"
                              class="text-white home-btn"
                        >Admin Dashboard
                        </Link>

                        <v-expansion-panel style="background-color: #494949;border-radius:0;"
                                           v-for="panel in panels"
                                           :title="panel.section"
                        >

                            <v-expansion-panel-text v-for="item in panel.items"
                            >
                                <v-btn style="background-color: #838383;font-size:1rem;border-radius: 0;"
                                       @click="opener = !opener"
                                       :class=
                                           "[
                                           (item.link.includes('/create') || item.link.includes('/edit')) && this.$page.url.includes(item.link) ? 'active' :  '' ,
                                           this.$page.url.includes(item.link) && !(this.$page.url.includes('/create') || this.$page.url.includes('/edit') || this.$page.url.includes('/recipes')) ? 'active' : '',
                                           this.$page.url == item.link ? 'active' : '',
                                            ]"
                                       class="text-white"
                                       :prepend-icon="item.icon" :href="item.link">{{ item.title }}
                                </v-btn>
                            </v-expansion-panel-text>

                        </v-expansion-panel>

                    </v-expansion-panels>

                </v-navigation-drawer>
            </v-app>


        </div>
        <div class="menu">
            <button v-if="this.$page.props.auth" @click="notificationOpener = !notificationOpener"
                    class="notification">
                <v-icon v-if="notifications.length == 0" style="color:#bebebe;">mdi-bell-outline</v-icon>
                <v-icon v-else style="color:white;">mdi-bell-ring</v-icon>
            </button>
            <v-app v-if="this.$page.props.auth"
                   style="position:absolute;">

                <v-navigation-drawer :key="$props.pageUrl"
                                     v-model="notificationOpener"
                                     temporary=""
                                     location="right"
                                     style="top:0;width:320px;background-color: rgb(43,43,43);border: none;z-index:9999">
                    <div class="space-notification">
                        <button @click="notificationOpener = !notificationOpener" class="opener">
                            <img src="../../../public/close.svg" alt="menu">
                        </button>
                    </div>
                    <v-divider style="color:white;margin: 0;"></v-divider>

                    <div class="notifications">
                        <div style="display:grid;place-items: center"
                             :style="notifications.length > 0 ? 'grid-template-columns: 4fr 1fr;' : 'grid-template-columns:1fr'">
                            <h3 class="text-white text-center m-3 fw-bold">Notifications
                            </h3>
                            <v-btn
                                @click="markAsRead" v-if="notifications.length > 0" variant="flat"
                                icon="mdi-delete"></v-btn>
                        </div>
                        <v-divider style="color:white;margin: 0;"></v-divider>


                        <div v-for="notification in notifications" class="notification-card">
                            <p class="notification-text">
                                {{
                                    notification.data.message ? notification.data.message : JSON.parse(notification.data).message
                                }}
                            </p>


                        </div>

                    </div>

                </v-navigation-drawer>
            </v-app>
        </div>


        <div v-if="!$page.props.auth" class="profile-section">

            <Link class="btn btn-outline-light" href="/guest/public">Public</Link>
            <Link class="btn btn-outline-light" href="/login">Login</Link>
            <Link class="btn btn-outline-light" href="/register">Register</Link>

        </div>
    </header>
</template>

<script>
import Pusher from "pusher-js";
import {Inertia} from "@inertiajs/inertia";

export default {
    name: "Header.vue",
    components: {},
    props: {
        title: String,
        pageUrl: String,
        notifications: {
            type: [Object, Array],
        },

    },
    data() {
        return {
            pic: this.$page.props.auth && this.$page.props.auth.user.picture ? this.$page.props.auth.user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png",
            opener: false,
            notificationOpener: false,
            panels: [
                {
                    section: "Recipes",
                    items: [
                        {
                            link: "/recipe/create",
                            title: "Create new recipe",
                            icon: "mdi-plus-box"
                        },
                        {
                            link: "/recipes/cooking",
                            title: "Get cooking ideas",
                            icon: "mdi-clock"
                        },
                        {
                            link: "/recipe",
                            title: "List of all recipes",
                            icon: "mdi-book-open-variant"
                        },
                        {
                            link: "/favorites",
                            title: "My favorites",
                            icon: "mdi-heart"
                        },
                        {
                            link: "/like",
                            title: "Liked Recipes",
                            icon: "mdi-thumb-up"
                        },
                        {
                            link: "/myshared",
                            title: "Shared recipes",
                            icon: "mdi-share"
                        },
                        {
                            link: "/sharedwithme",
                            title: "Recipes shared with me",
                            icon: "mdi-reply"
                        }
                    ],
                    value: 0,

                },
                {
                    section: "Collections",
                    items: [
                        {
                            link: "/collection/create",
                            title: "Create new collection",
                            icon: "mdi-plus-box"
                        },
                        {
                            link: "/collection",
                            title: "My collections",
                            icon: "mdi-bag-personal"
                        }
                    ],
                    value: 1,

                },
                {
                    section: "User",
                    items: [
                        {
                            link: `/user/${this.$page.props.auth?.user.id}/edit`,
                            title: "My profile",
                            icon: "mdi-account"
                        },
                        {
                            link: "/follow",
                            title: "Followers/Following",
                            icon: "mdi-account-multiple"
                        },
                        {
                            link: "/review",
                            title: "My reviews",
                            icon: "mdi-star"
                        },

                    ],
                    value: 2,
                },
            ],
            active: null,


        }
    },
    watch: {
        "$page.url": function () {
            this.opener = false;
            this.notificationOpener = false;
            this.active = this.setItemAndPanelActive();
        },


    },
    mounted() {
        this.active = this.setItemAndPanelActive();
    },


    methods: {
        /**
         * Loop through panels and panel items to check if any item is a link to the current page and makes it active,
         * so the panel will be expanded when menu is opened
         */
        setItemAndPanelActive() {
            let url = this.$page.url;
            let value;
            this.panels.forEach(function (panel, i) {

                panel.items.forEach(function (item) {
                    if ((item.link.includes('/create') || item.link.includes('/edit')) && url.includes(item.link)) {
                        value = i;
                    }
                    if (url.includes(item.link) && !(url.includes('/create') || url.includes('/edit'))) {
                        value = i;
                    }
                })
            });
            return value;
        },

        /**
         * Marks the notifications as read.
         *
         * @return {void}
         */
        markAsRead() {
            sessionStorage.removeItem('notifications');
            Inertia.put('/notifications');
        },
    }


}
</script>

<style scoped>

.notification-card {
    background-color: #bdbdbd;
    border-bottom: 1px solid white;
    padding: 1em;
}

.notification-text {
    margin: 0;
}

.opener {
    width: fit-content;

}

.space-notification {
    padding-right: 27.5px;
    padding-left: 0;
    height: 100px;
    display: grid;
    align-items: center;
    justify-items: end;
}

.notification {
    width: fit-content;
    position: absolute;
    right: 27.5px;
}

.notification:deep( .v-icon) {
    font-size: 45px;
}

.space {
    height: 100px;
    display: grid;
    align-items: center;
    justify-items: start;
    padding-left: 27.5px;
    padding-top: 1.5px;
}

.side-list:deep( .v-list-item) {
    font-size: 1.35rem;
    color: white;

}

.v-list-item--active {
    background-color: #5c5e60 !important;
    color: yellow !important;
    font-weight: bold;

}

.side-list:deep(i) {
    opacity: 1;
    height: 50px;
    /*Because of padding on parent item*/
}

.link {
    font-size: 1em;
    color: white;

    font-family: roboto, sans-serif;
}

.side-user {
    font-size: 1.5rem;
    color: white;
    font-family: roboto, sans-serif;
    font-style: italic;
}

.side-user:deep( .v-avatar ) {
    height: 75px;
    width: 75px;
}

header {

    display: grid;
    height: 100px;
    align-items: center;
    grid-template-columns: 1fr min-content;
    position: relative;
    grid-auto-flow: column;
    padding: 0 2em 0 0;

    background-color: #791212;
    border-bottom: 2px solid black;
}

header .menu {

    height: 100px;
    width: 100px;
    display: grid;
    align-items: center;
    padding-left: 27.5px;
}

.menu > a {

    width: fit-content;
    height: fit-content;
}

header .logo {

    position: absolute;

    height: 100px;


}

.logo-holder {
    height: 100px;
    width: 100px;
    top: 0;
    right: calc(50% - 3em);

    position: absolute;
}

h2 {
    font-family: roboto, sans-serif;
    margin: 0;
    font-style: italic;
}


.profile-pic, .profile-section > a[href="/logout"] {
    width: 60px;
    height: 60px;
    border-radius: 50%;

}

.profile-pic {
    object-fit: cover;
    outline: 2px solid white;
}

.profile-section {
    display: grid;
    gap: 20px;
    grid-auto-flow: column;

}

.profile-section > button {
    height: fit-content;
    align-self: center;
    font-size: 1.25rem;
}

.active {
    font-weight: bold;
    color: #03d1f5 !important;
    background-color: rgba(42, 42, 42, 0.78) !important;

}

.profile-section > a {
    font-size: 1.25rem;
}

.logout-btn:deep( .v-btn__append) {
    margin-inline-start: 0 !important;
}


.link {

    font-style: italic;

    border-radius: 5px;
    display: grid;
    box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
}

.link:hover {
    background-color: rgb(0, 200, 255, 0.2);
}

.v-list-item {
    font-size: 1.5rem !important;
}

.recipes-actions,
.collections-actions,
.user-actions,
.public-actions {
    display: grid;
    gap: 10px;
    grid-auto-rows: 75px;
}

.v-list-item:deep( .v-list-item__spacer ) {
    width: 10px !important;
}

.v-expansion-panel:deep( .v-expansion-panel-text__wrapper ) {
    padding: 0;
    width: 100%;
    height: fit-content;

}

.v-expansion-panel:deep( .v-btn) {
    width: 100%;
    height: 60px;
}

.v-expansion-panel:deep(.v-expansion-panel-title) {
    font-size: 1.25rem;
    height: 70px;
    color: white;
    font-weight: 550;
}

.home-btn:hover {
    background-color: rgba(249, 251, 252, 0.2) !important;
}

.v-expansion-panel:deep( .v-btn:hover) {
    background-color: rgba(12, 48, 58, 0.2) !important;
}

@media (max-width: 786px) {
    .menu .v-navigation-drawer {
        width: 100% !important;
    }
}

</style>
