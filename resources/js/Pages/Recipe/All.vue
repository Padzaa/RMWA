<template>

    <Head>
        <title>{{title}}</title>
    </Head>

        <Filtering :recipes="recipes"
                   :categories="categories"
                   :ingredients="ingredients"
                   :filters="filtersData"
                   :collections="collections"/>

        <GridNet :recipes="recipes.data"
                 :auth="this.$attrs.auth"/>

        <Paginator :recipes="recipes"/>

</template>

<script>
import Card from '../../Shared/Card.vue';
import Filter from '../../Shared/Filter.vue';
import {Inertia} from '@inertiajs/inertia'
import GridNet from "../../Shared/GridNet.vue";
import Paginator from "../../Shared/Paginator.vue";
import Filtering from "../../Shared/Filtering.vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
    props: {
        recipes: Object,
        categories: Object,
        ingredients: Object,
        filtersData: Object,
        rating: 0,
        collections: Object,
        title: String,
    },

    components: {
        Filtering,
        Paginator,
        Card, Filter, GridNet
    }

}
</script>
<style scoped>

</style>
