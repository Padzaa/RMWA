<script>
import GridNet from "../../Shared/GridNet.vue";
import Paginator from "../../Shared/Paginator.vue";

export default {
    props: {
        recipes: Object,
        title: String
    },
    components: {
        Paginator,
        GridNet
    }
}
</script>

<template>

    <Head>
        <title>Shared Recipes</title>
    </Head>
    <div class="c">
        <h1>{{ title }}</h1>
        <GridNet :recipes="recipes.data" :auth="this.$attrs.auth"></GridNet>
        <Paginator :recipes="recipes"></Paginator>
    </div>

</template>

<style scoped>
.c {
    padding: 2em;
}

.c > h1 {
    text-align: center;
}
</style>
