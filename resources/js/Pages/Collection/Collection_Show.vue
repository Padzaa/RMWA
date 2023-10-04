<script>
export default{
    props:{
        collection:Object
    }
}
</script>

<template>
    <Header></Header>
    <Head>
        <title>{{collection.name}}</title>
    </Head>

    <div class="collection-box">
        <div class="collection form-control">
            <div class="header">
                <h1>{{collection.name}}</h1>
            </div>
            <div class="body">
                <h5>Number of recipes: {{collection.recipes.length}}</h5>
                <h5>All Recipes:</h5>
                <ul>
                    <li v-for="recipe in collection.recipes">
                        <p><a :href="'/recipe/' + recipe.id">{{recipe.title}}</a></p>
                    </li>
                </ul>
            </div>
            <div class="footer">
                <div class="actions">
                    <Link class="btn btn-primary" :href="'/collection/' + collection.id + '/edit'">Edit</Link>
                    <Link class="btn btn-danger" as="button" method="DELETE" :href="'/collection/' + collection.id">Delete</Link>
                </div>
            </div>
            </div>
    </div>



</template>

<style scoped>
.collection-box{
    display:grid;
    height: 100%;
    justify-content: center;
    padding:3em;
}
.collection{
    width:460px;
    border-radius: 25px;
    padding: 1em 1.5em;
    height: fit-content;
}
.collection .header{
    font-style: italic;

}
h5{
    margin: 1em 0;
}
ul p>a{
    font-size: 18px;
}

div.actions {
    align-self: end;
    align-items: center;
    display: flex;
    gap: 10px;

}

div.actions > a {
    text-align: center;
    height: fit-content;
}
</style>
