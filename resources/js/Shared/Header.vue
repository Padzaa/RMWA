<template>

    <header>
        <Link href="/" class="logo-holder">
            <img src="https://png.pngtree.com/png-vector/20220709/ourmid/pngtree-hat-chef-logo-png-image_5820915.png"
                 alt="logo" class="logo"></Link>
        <div class="menu">
            <Link v-if="this.$page.props.auth" as="button" @click="opener = !opener" class="opener">
                <img src="../../../public/menu.svg" alt="menu">
            </Link>
            <v-app v-if="this.$page.props.auth" style="position:absolute;">

                <v-navigation-drawer :key="$props.pageUrl"
                                     style="top:0;width:320px;background-color: rgb(43,43,43);border: none;"
                                     v-model="opener"
                                     temporary

                >
                    <div class="space">
                        <Link as="button" @click="opener = !opener" class="opener">
                            <img src="../../../public/close.svg" alt="menu">
                        </Link>
                    </div>
                    <v-divider style="color:white;margin: 0;"></v-divider>
                    <div style="display: grid;">
                        <Link @click="opener = !opener" style="display:grid;"
                              :href="'/user/'+ this.$page.props.auth.user.id +'/edit'">
                            <v-list-item value="user" class="side-user"
                                         :class="$props.pageUrl == '/user/'+ this.$page.props.auth.user.id +'/edit' ? 'v-list-item--active' : ''"
                                         style="padding: 15px 0 15px 1em;"
                                         :prepend-avatar="pic"

                            >{{ this.$page.props.auth.user.firstname + " " + this.$page.props.auth.user.lastname }}

                            </v-list-item>

                        </Link>
                        <v-divider style="color:white;margin: 0;"></v-divider>
                        <div style="display: grid;padding: 10px 0;">
                            <Link href="/logout" method="POST" style="justify-self:center;width:90%;">
                                <v-btn class="logout-btn" style="justify-self:center;font-size: 1em;width:100%;"
                                       variant="outlined" color="white"
                                       append-icon="mdi-logout">Logout
                                </v-btn>
                            </Link>
                        </div>

                    </div>


                    <v-divider style="color:white;;margin: 0;"></v-divider>

                    <v-list class="side-list" density="compact" nav>


                        <Link @click="opener = !opener" class="link" href="/">
                            <v-list-item value="1" prepend-icon="mdi-home"
                                         :class="$props.pageUrl== '/' ? 'v-list-item--active' : ''">Home
                            </v-list-item>
                        </Link>


                        <Link @click="opener = !opener" class="link" href="/recipe"
                        >
                            <v-list-item value="2" prepend-icon="mdi-book-open-variant"
                                         :class="$props.pageUrl.includes('/recipe') && this.$page.component == 'Recipe/All' ? 'v-list-item--active' : ''">
                                Recipes
                            </v-list-item>
                        </Link>


                        <Link @click="opener = !opener" class="link" href="/collection"
                        >
                            <v-list-item value="3" prepend-icon="mdi-bag-personal"
                                         :class="$props.pageUrl.includes('/collection') ? 'v-list-item--active' : ''">
                                Collections
                            </v-list-item>
                        </Link>


                        <Link @click="opener = !opener" class="link" href="/favorites"
                        >
                            <v-list-item value="4" prepend-icon="mdi-heart"
                                         :class="$props.pageUrl == '/favorites' ? 'v-list-item--active' : ''">
                                Favorites
                            </v-list-item>
                        </Link>


                        <Link @click="opener = !opener" class="link" href="/public"
                        >
                            <v-list-item value="5" prepend-icon="mdi-earth"
                                         :class="$props.pageUrl.includes('/public') ? 'v-list-item--active' : ''">Public
                            </v-list-item>
                        </Link>


                        <Link @click="opener = !opener" class="link" href="/recipe/create"

                        >
                            <v-list-item value="6" prepend-icon="mdi-receipt-text-plus-outline"
                                         :class="$props.pageUrl == '/recipe/create' ? 'v-list-item--active' : ''">
                                Create Recipe
                            </v-list-item>
                        </Link>

                    </v-list>
                </v-navigation-drawer>
            </v-app>


        </div>


        <div v-if="!$page.props.auth" class="profile-section">

            <Link class="btn btn-outline-light" href="/public">Public</Link>
            <Link class="btn btn-outline-light" href="/login">Login</Link>
            <Link class="btn btn-outline-light" href="/register">Register</Link>

        </div>
    </header>
</template>

<script>


export default {
    name: "Header.vue",
    components: {},
    props: {
        title: String,
        pageUrl: String
    },
    data() {
        return {
            pic: this.$page.props.auth && this.$page.props.auth.user.picture ? this.$page.props.auth.user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png",
            opener: false,
        }
    }, watch: {
        "$page.url": function () {
            this.opener = false;
        }
    },


}
</script>

<style scoped>
.opener {
    width: fit-content;

}

.space {
    height: 100px;


    display: grid;
    align-items: center;
    justify-items: start;
    padding-left: 27.5px;
    padding-top: 1.5px;
}

.side-list >>> .v-list-item {
    font-size: 1.35rem;
    color: white;

}

.v-list-item--active {
    background-color: #5c5e60 !important;
    color: yellow !important;
    font-weight: bold;

}

.side-list >>> i {
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

.side-user >>> .v-avatar {
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

}

.profile-section > a {
    font-size: 1.25rem;
}

.logout-btn >>> .v-btn__append {
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

.v-list-item >>> .v-list-item__spacer {
    width: 10px !important;
}
</style>
