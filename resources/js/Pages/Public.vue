<script>
import GridNet from "../Shared/GridNet.vue";

export default {
    components: {GridNet},
    props: {
        recipes: Object
    },
}
</script>

<template>

    <Head>
        <title>Public Recipes</title>
    </Head>
    <div class="container">
        <h1 class="text-center">Public Recipes</h1>
        <GridNet :recipes="recipes" :auth="this.$attrs.auth"></GridNet>
    </div>
</template>

<style scoped>

</style>
