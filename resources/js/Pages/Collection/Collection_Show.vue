<script>
export default {
    props: {
        collection: Object
    }
}
</script>

<template>

    <Head>
        <title>{{collection.name}}</title>
    </Head>

    <div class="collection-box">
        <div class="collection form-control">
            <div class="header">
                <h1>{{ collection.name }}</h1>
            </div>
            <div class="body">
                <h5>Number of recipes: {{ collection.recipes.length }}</h5>
                <h5>All Recipes:</h5>
                <ul>
                    <li v-for="recipe in collection.recipes">
                        <p><a :href="'/recipe/' + recipe.id">{{ recipe.title }}</a></p>
                    </li>
                </ul>
            </div>
            <div class="footer">
                <div class="actions">
                    <Link
                        :href="'/collection/' + collection.id + '/edit'"
                        method="GET">
                        <img src="../../../../public/edit.svg" alt="edit">
                    </Link>
                    <Link as="button" method="DELETE"
                          :href="'/collection/' + collection.id">
                        <img src="../../../../public/delete.svg" alt="delete">
                    </Link>
                </div>
            </div>
        </div>
    </div>


</template>

<style scoped>
.collection-box {
    display: grid;
    height: 100%;
    justify-content: center;
    padding: 3em;
}

.collection {
    width: 460px;
    border-radius: 25px;
    padding: 1em 1.5em;
    height: fit-content;
}

.collection .header {
    font-style: italic;
    text-transform: capitalize;
}

h5 {
    margin: 1em 0;
}

ul p > a {
    font-size: 18px;
}

div.actions {
    align-self: end;
    align-items: center;
    display: flex;
    gap: 15px;
    justify-content: end;

}

div.actions > a {
    text-align: center;
    height: fit-content;
}
</style>
