<template>

    <div id="menu">
        <div v-if="this.$page.props?.auth?.user.is_admin == 1" class="admin">
            <h1>Admin</h1>
            <div class="admin-actions">
                <Link class="link" v-for="link in admin" :href="link.link">
                    <v-list-item :prepend-icon="link.icon">{{ link.title }}</v-list-item>
                </Link>
            </div>
        </div>
        <div class="recipes">
            <h1>Recipes <!--<v-icon>mdi-book-open-variant</v-icon>--></h1>
            <div class="recipes-actions">
                <Link class="link" v-for="link in recipes" :href="link.link">
                    <v-list-item :prepend-icon="link.icon">{{ link.title }}</v-list-item>
                </Link>
            </div>
        </div>

        <div class="collections">
            <h1>Collection <!--<v-icon>mdi-bag-personal</v-icon>--></h1>
            <div class="collections-actions">
                <Link class="link" v-for="link in collections" :href="link.link">
                    <v-list-item :prepend-icon="link.icon">{{ link.title }}</v-list-item>
                </Link>
            </div>
        </div>
        <div class="public">
            <h1>Public <!--<v-icon>mdi-earth</v-icon>--></h1>
            <div class="public-actions">
                <Link class="link" v-for="link in public" :href="link.link">
                    <v-list-item :prepend-icon="link.icon">{{ link.title }}</v-list-item>
                </Link>
            </div>
        </div>
        <div class="user">
            <h1>User <!--<v-icon>mdi-account</v-icon>--></h1>
            <div class="user-actions">
                <Link class="link" v-for="link in user" :href="link.link">
                    <v-list-item :prepend-icon="link.icon">{{ link.title }}</v-list-item>
                </Link>
            </div>
        </div>

        <!--        <Link href="/sendmail" method="POST">SEND MAIL</Link>-->
    </div>
</template>

<script>


export default {
    name: "HomeMenu.vue",
    data() {
        return {
            admin: this.$page.props?.auth?.user.is_admin == 1 ? [
                {
                    link: "/dashboard",
                    title: "Dashboard",
                    icon: "mdi-view-dashboard"
                }
            ] : [],
            recipes: [
                {
                    link: "/recipe/create",
                    title: "Create new recipe",
                    icon: "mdi-plus-box"
                },
                {
                    link: "/recipes/cooking",
                    title: "Cook now",
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
            collections: [
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
            user: [
                {
                    link: `/user/${this.$page.props.auth.user.id}/edit`,
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
                {
                    link: "/message",
                    title: "Messages",
                    icon: "mdi-message"
                },

            ],
            public: [
                {
                    link: "/guest/public",
                    title: "Public recipes",
                    icon: "mdi-earth"
                }
            ]

        }
    }
}
</script>

<style scoped>
#menu {
    padding: 2em;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5em;
    height: fit-content;
}

h1 {
    font-family: Roboto, sans-serif;
    font-weight: 700;
    text-align: center;
    color: #4a4a4b;
    font-size: 2rem;
}

.link {
    font-style: italic;
    border-radius: 1px;
    display: grid;
    box-shadow: rgba(50, 50, 93, 0.25) 0 6px 12px -2px, rgba(0, 0, 0, 0.3) 0 3px 7px -3px;
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
.public-actions,
.admin-actions {
    display: grid;
    gap: 10px;
    grid-auto-rows: 75px;
}

h2 {
    text-align: center;
}

.v-list-item:deep( .v-list-item__spacer ) {
    width: 10px !important;
}
</style>
