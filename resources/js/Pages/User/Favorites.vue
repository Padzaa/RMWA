<script>
import Header from "../../Shared/Header.vue";
import GridNet from "../../Shared/GridNet.vue";
import Paginator from "../../Shared/Paginator.vue";

export default {
    components: {Paginator, GridNet, Header},
    props: {
        recipes: Array
    },
}
</script>

<template>

    <Head>
        <title>Favorites</title>
    </Head>
    <div class="container">
        <h1 class="text-center">Favorites</h1>
        <GridNet :recipes="recipes.data" :auth="this.$attrs.auth"></GridNet>
        <Paginator :recipes="recipes"></Paginator>
    </div>


</template>

<style scoped>
.grid-net {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    padding: 1em 2em;
    gap: 1em;
    grid-auto-rows: auto;
    justify-items: center;
}

p {
    font-style: italic;

    color: gray;
    text-align: center;
    margin-bottom: 0;
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

.container {
    padding: 2em 0;
}
</style>
