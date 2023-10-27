<script>
import {Inertia} from "@inertiajs/inertia";

export default {
    props: {
        recipes: Object,
    },
    data() {
        return {
            page: parseInt(new URLSearchParams(this.$page.url).get('page')) || 1
        }
    },
    methods: {
        changePage() {
            Inertia.get('/recipe?page=' + this.page);
        }
    },
    mounted() {
        console.log(this.page);
    }
}
</script>

<template>
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
</style>
