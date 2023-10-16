<template>

    <Head>
        <title>{{title}}</title>
    </Head>
    <Filtering :recipes="recipes" :categories="categories" :ingredients="ingredients" :form="form" :collections="collections"></Filtering>
    <GridNet :recipes="recipes.data" :auth="this.$attrs.auth"></GridNet>
    <Paginator :recipes="recipes"></Paginator>
</template>

<script>
import Card from '../../Shared/Card.vue';
import Filter from '../../Shared/Filter.vue';
import {Inertia} from '@inertiajs/inertia'
import GridNet from "../../Shared/GridNet.vue";
import Paginator from "../../Shared/Paginator.vue";
import Filtering from "../../Shared/Filtering.vue";

export default {
    props: {
        recipes: Object,
        categories: Object,
        ingredients: Object,
        filterData: Object,
        rating: 0,
        collections: Object,
        title: String,
    },

    components: {
        Filtering,
        Paginator,
        Card, Filter, GridNet
    },
    data() {
        return {
            form: {
                categories: this.filterData.categories ? this.filterData.categories : [],
                ingredients: this.filterData.ingredients ? this.filterData.ingredients : [],
                favorites: this.filterData.favorites ? this.filterData.favorites : null,
                ratings: this.filterData.ratings ? this.filterData.ratings : [],
                order: this.filterData.order ? this.filterData.order : 'desc',
                search: this.filterData.search ? this.filterData.search : '',
                collections: this.filterData.collections ? this.filterData.collections : [],
            },

        }
    }
}
</script>
<style scoped>
</style>
