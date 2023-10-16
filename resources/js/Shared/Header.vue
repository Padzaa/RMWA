<template>

    <header>
        <div class="left-side" >

            <img src="https://png.pngtree.com/png-vector/20220709/ourmid/pngtree-hat-chef-logo-png-image_5820915.png" alt="">
            <div class="links" v-if="$page.props.auth">
                <Link class="link" href="/" :class="this.$page.component == 'Welcome' ? 'active' : ''">Home</Link>
                <Link class="link" href="/recipe" :class="this.$page.url.includes('/recipe') && this.$page.component == 'Recipe/All' ? 'active' : ''">Recipes</Link>
                <Link class="link" href="/collection" :class="this.$page.component == 'Collection/Collections' ? 'active' : ''">Collections</Link>
                <Link class="link" href="/favorites" :class="this.$page.component == 'User/Favorites' ? 'active' : ''">Favorites</Link>
                <Link class="link" href="/public" :class="this.$page.url.includes('/public') ? 'active' : ''">Public</Link>

                <Link class="link" href="/recipe/create"
                      :class="this.$page.component == 'Recipe/Recipe_Create' ? 'active' : ''">Create Recipe
                </Link>

            </div>

        </div>

        <div v-if="$page.props.auth" class="profile-section">
            <Link :href="'/user/'+$page.props.auth.user.id+'/edit'"><img :src="pic" alt="" class="profile-pic"/></Link>

            <Link class="btn btn-outline-light" href="/logout" method="POST" as="button">Logout</Link>
        </div>
        <div v-else="$page.props.auth" class="profile-section">

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

    },
    data() {
        return {
            pic: this.$page.props.auth && this.$page.props.auth.user.picture ? this.$page.props.auth.user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png"
        }
    },


}
</script>

<style scoped>
header {

    display: grid;
    height: 100px;
    align-items: center;

    justify-content: space-between;
    grid-auto-flow: column;
    padding: 0 2em;
    background-color: #791212;
    border-bottom: 2px solid black;
}

h2 {
    font-family: roboto, sans-serif;
    margin: 0;
    font-style: italic;
}

.links {

    display: grid;
    gap: 25px;
    grid-auto-flow: column;
    place-items: center;
    padding: 1em;
}

.profile-pic, .profile-section > a[href="/logout"] {
    width: 60px;
    height: 60px;
    border-radius: 50%;

}

.profile-pic {
    object-fit: cover;
    outline:2px solid white;
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
    color:#03d1f5 !important;
    border: 3px solid #03d1f5 !important;
}
.profile-section > a {
    font-size: 1.25rem;
}
.links a {
    font-size: 1.5rem;
    color: white;
    padding: 5px 10px;
    border: 2px solid white;
    border-radius: 8px;
    font-family: roboto, sans-serif;
}
.left-side{
    display: grid;
    grid-template-columns: 1fr auto;
}
.left-side > img{
    height: 100px;
}
</style>
