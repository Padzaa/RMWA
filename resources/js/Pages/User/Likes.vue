<script>

import GridNet from "../../Shared/GridNet.vue";
import Paginator from "../../Shared/Paginator.vue";

export default {
    props: {
        likes: Object
    },
    components: {
        Paginator,
        GridNet,
    }
}
</script>

<template>

    <Head>
        <title>Liked Recipes</title>
    </Head>
    <div class="contain">
        <h1 class="text-center">Liked recipes</h1>
        <GridNet :recipes="likes.data" :auth="this.$attrs.auth"></GridNet>
        <Paginator :recipes="likes"/>
    </div>


</template>

<style scoped>
.contain {
    padding: 2em 0;
}
</style>
