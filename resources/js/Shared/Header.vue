<template>

    <header>
        <div v-if="$page.props.auth">

            <h2>{{$page.props.auth.user.firstname + " " + $page.props.auth.user.lastname}}</h2>
            <div class="links">
                <Link class="links" href="/" :class="this.$page.component == 'Welcome' ? 'active' : ''">Home</Link>|
                <Link class="links" href="/recipe/create" :class="this.$page.component == 'Recipe/Recipe_Create' ? 'active' : ''">Create Recipe</Link>|
            </div>

        </div>
      <div v-else="$page.props.auth">

      </div>
        <div v-if="$page.props.auth" class="profile-section">
            <Link :href="'/user/'+$page.props.auth.user.id+'/edit'" ><img :src="pic" alt="" class="profile-pic"/></Link>
            <Link as="button" class="btn btn-light" href="/public" >Public</Link>
            <Link class="btn btn-danger" href="/logout" method="POST" as="button">Logout</Link>
        </div>
      <div v-else="$page.props.auth" class="profile-section">


        <Link class="btn btn-primary" href="/login" >Login</Link>
        <Link class="btn btn-light" href="/public" >Public</Link>
        <Link class="btn btn-dark" href="/register" >Register</Link>
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
  background: rgb(232,106,3);
  background: linear-gradient(174deg, rgba(232,106,3,1) 0%, rgba(255,248,0,1) 100%);
  border-bottom: 2px solid black;
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
.profile-pic,.profile-section>a[href="/logout"]{
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
.active{
  font-weight: bold;

}
.links a{
  font-size:1.25rem;
}
</style>
