<template>
    <Header/>
    <Head>
        <title>{{recipe.title}}</title>
    </Head>
    <div class="show-recipe">
        <div class="recipe">
            <Link class="heart" method="PUT" :href="'/recipe/' + recipe.id + '/favorite'" preserve-scroll>
                <svg width="48px" height="48px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path :class="{'heart-svg':recipe.is_favorite}"
                          d="M12 20C12 20 21 16 21 9.71405C21 6 18.9648 4 16.4543 4C15.2487 4 14.0925 4.49666 13.24 5.38071L12.7198 5.92016C12.3266 6.32798 11.6734 6.32798 11.2802 5.92016L10.76 5.38071C9.90749 4.49666 8.75128 4 7.54569 4C5 4 3 6 3 9.71405C3 16 12 20 12 20Z"
                          stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </Link>
            <h1>{{ recipe.title }}</h1>
            <h6>DESCRIPTION:</h6>
            <p class="description">{{ recipe.description }}</p>
            <h6>INGREDIENTS:</h6>
            <ul>
                <li v-for="ingredient in recipe.ingredients">{{ ingredient.name }}</li>
            </ul>
            <h6>INSTRUCTIONS:</h6>
            <p class="instructions"> {{ recipe.instructions }}</p>
        </div>
    </div>


</template>

<script>

export default {
    name: "Show.vue",
    props: {
        recipe: Object,
        ingredients: Array,
    },
    components: {}
}
</script>

<style scoped>
.show-recipe {
    display: grid;
    padding: 3em 0;
    justify-items: center;
    height: 100%;
}

.recipe {
    min-width: 750px;
    max-width: 750px;
    border: 1px solid gray;
    border-radius: 25px;
    padding: 1.3em 2em;
    position: relative;
}

.description {
    font-style: italic;
    text-transform: capitalize;
    font-size: 1.2rem;
    color: gray;
}

h1 {
    text-transform: capitalize;
}

.instructions {
    font-size: 1.1rem;
    text-transform: capitalize;
}

.heart {
    position: absolute;
    top: 1.3em;
    right: 2em;


}

</style>
