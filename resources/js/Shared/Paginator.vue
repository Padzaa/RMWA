<script>
import {Inertia} from "@inertiajs/inertia";

export default {
    props: {
        recipes: Object,
    },
    data() {
        return {
            page: this.recipes.current_page
        }
    },
    methods: {
        /**
         * Change the page of the recipe pagination.
         */
        changePage() {
            Inertia.get(this.recipes.path + '?page=' + this.page);
        }
    },
}
</script>

<template>

    <p v-if="recipes.data.length == 0" class="text-center">0 records found</p>
    <!-- :length = "recipes.links.length - 2" (-2 is because the length of the links also contains links for previous and next page) -->
    <v-pagination
        v-model="page"
        :length="recipes.links.length - 2"
        :total-visible="5"
        @click="changePage"
    ></v-pagination>


</template>

<style scoped>
#paginator {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding: 1em 0;
}

p {
    font-style: italic;

    color: grey;
    text-align: center;
    margin-bottom: 0;
}

.v-pagination >>> .v-pagination__list {
    padding-left: 0;
}
</style>
