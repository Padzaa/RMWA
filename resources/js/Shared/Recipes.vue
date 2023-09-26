<template>

    <div id="recipes_menu">
        <Link href="/recipe/create">
            <div class="card">
                <h2>Add New</h2>
                <img src="https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/external-recipe-foodies-flaticons-lineal-color-flat-icons-2.png" alt="picture">

            </div>
        </Link>

        <Link href="/recipe">
            <div class="card">
                <h2>See All</h2>
                <img src="https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/external-recipe-foodies-flaticons-lineal-color-flat-icons-2.png" alt="picture">

            </div>
        </Link>


    </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import Card from "./Card.vue";
export default {
    name: "Recipes.vue",
    components:{
        Link,
    }
}
</script>

<style scoped>
#recipes_menu{
    padding:2em;
    display:grid;
    grid-auto-flow: column;
    grid-template-columns: 300px 300px;
    gap: 1.5em;
    height: fit-content;

}
h2{
    text-align:center;
}
img{
    height:100%;
    width:100%;
}
div.card{
    height:100%;
    padding:1em;
}
</style>
