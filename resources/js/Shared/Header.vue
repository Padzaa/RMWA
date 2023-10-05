<template>

    <header>
        <div>
            <h2>{{ $page.props.auth.user.firstname }} {{ $page.props.auth.user.lastname }}</h2>
            <div class="links">
                <Link class="links" href="/">Home</Link>|
                <Link class="links" href="/recipe/create">Create Recipe</Link>|

            </div>

        </div>
        <div class="profile-section">
            <Link :href="'/user/'+$page.props.auth.user.id+'/edit'"><img :src="pic" alt="" class="profile-pic"/></Link>

            <Link class="btn btn-danger" href="/logout" method="POST" as="button">Logout</Link>
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
    data(){
        return {
            pic: this.$page.props.auth.user.picture ? this.$page.props.auth.user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png"
        }
    }


}
</script>

<style scoped>
header {
    background-color: lightgray;
    display: grid;
    height: 100px;
    align-items: center;
    justify-content: space-between;
    grid-auto-flow: column;
    padding: 0 2em;
}

h2 {
    font-family: roboto, sans-serif;
    margin: 0;
    font-style: italic;
}

.links {
    font-size: 1.15rem;
    display: flex;
    gap: 10px;
}
.profile-pic,.profile-section>a{
    width:60px;
    height:60px;
    border-radius:50%;
}
.profile-pic{
  object-fit: cover;
}
.profile-section{
    display:grid;
    gap:10px;
    grid-auto-flow: column;
}
.profile-section>button{
    height:fit-content;
    align-self:center;
}
</style>
