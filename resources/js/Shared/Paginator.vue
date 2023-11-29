<script>
import {Inertia} from "@inertiajs/inertia";

export default {
    props: {
        recipes: {
            type: [Object, Array],
        },
    },
    data() {
        return {
            page: this.recipes.current_page > this.recipes.last_page ? this.changePage() : this.recipes.current_page,
            number_per_page: [10, 25, 50, 100],
            per_page: new URLSearchParams(window.location.search).get("per_page") ? +new URLSearchParams(window.location.search).get("per_page") : 10,
        }
    },
    watch: {
      "per_page": function () {
          this.perPage();
      }
    },
    methods: {
        /**
         * Change the page of the recipe pagination.
         */
        changePage(){
            Inertia.get(this.$page.url, {page: this.page});
        },
        /**
         * Paginate per page
         */
        perPage(){
            Inertia.get(this.$page.url, {per_page :this.per_page});
        }
    }
}
</script>

<template>

    <p v-if="recipes.data.length == 0" class="text-center">0 records found</p>
    <!-- :length = "recipes.links.length - 2" (-2 is because the length of the links also contains links for previous and next page) -->
    <div style="display: grid;grid-template-columns: 1fr min-content;position:relative;">


    <v-pagination
        v-model="page"
        :length="recipes.links.length - 2"
        :total-visible="5"


        @click="changePage"
    ></v-pagination>

    <v-select style="width:100px;position:absolute;right:0" v-model="per_page" :items="number_per_page"
    label="Per Page" @change="perPage"></v-select>
    </div>
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
