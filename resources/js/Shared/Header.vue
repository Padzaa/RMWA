<template>

    <header>
        <div>
            <h2>Welcome, {{$page.props.auth.user.username}} {{$page.props.auth.user.lastname}}</h2>
            <div class="links">
                <Link class="links" href="/">Home</Link>
                <Link class="links" href="/recipe">Recipe</Link>

            </div>

        </div>
        <div>
            <Link class="btn btn-danger" href="/logout" method="POST" as="button">Logout</Link>
        </div>
    </header>
</template>

<script>

export default {
    name: "Header.vue",
    components:{

    },
    props: {
        title:String
    }

}
</script>

<style scoped>
header{
    background-color:lightgray;
    display: grid;
    height:100px;
    align-items: center;
    justify-content: space-between;
    grid-auto-flow: column;
    padding: 0 2em;
}
h2{
    font-family: roboto,sans-serif;
    margin:0;
    font-style: italic;
}
.links{
    font-size:1.15rem;
    display:flex;
    gap: 10px;
}

</style>
