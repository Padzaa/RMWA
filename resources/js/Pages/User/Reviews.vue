+
<script>
import Header from "../../Shared/Header.vue";

export default {
    components: {Header},
    props: {
        reviews: Object,

    },
    data() {
        return {
            rating: 0,
        }
    },
    methods: {
        normalDate(recipeDate) {
            //Converts a date to a string with the format YYYY-MM-DD
            const dateObject = new Date(recipeDate);
            const year = dateObject.getFullYear();
            const month = dateObject.getMonth() + 1; // Month is zero-based
            const day = dateObject.getDate();
            return `${day < 10 ? '0' : ''}${day}-${month < 10 ? '0' : ''}${month}-${year}`;

        }
    }
}
</script>

<template>

    <Head>
        <title>Reviews
        </title>
    </Head>

    <div class="cont">
    <h1>Reviews</h1>
    <div class="reviews">
        <template v-for="review in reviews.data">
            <div class="review-card">
                <div class="rating-hdr text-center">
                    <v-rating
                        class="ok"
                        v-model="review.rating"
                        bg-color="orange-lighten-1"
                        color="green"
                        disabled=""
                    ></v-rating>
                </div>
                <v-divider></v-divider>
                <h4>
                    <Link class="recipe_name" :href="'/recipe/'+review.recipe.id">{{ review.recipe.title }}</Link>
                </h4>

                <div class="review-comment">
                    <h5>Comment:</h5>
                    <p class="comment"><cite><q>{{ review.message }}</q></cite></p>
                </div>

                <span class="posted-at">Rated: {{ normalDate(review.created_at) }}</span>
            </div>

        </template>
    </div>

    </div>
    <div id="paginator">
        <p>Recipes from {{ reviews.from ? reviews.from : 0 }} to {{ reviews.to ? reviews.to : 0 }} of total {{
                reviews.total
            }}</p>
        Page:
        <template v-for="(link,index) in reviews.links">

            <Link v-if="index !== 0 && index !== (reviews.links.length - 1)"
                  :style="{
                'font-weight' : link.active ? 'bold' : 400,
                'color' : link.active ? 'red' : 'inherit'
                            }"
                  :href="link.url"

            >

                {{ link.label }}
            </Link>

        </template>
    </div>
</template>

<style scoped>
.cont{
    display: grid;
    padding: 2em;
}
.cont > h1{
    text-align: center;
}
#paginator {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding: 1em 0;
}

a {
    font-size: 1.1rem;
    width: fit-content;
}

.recipe_name {
    font-weight: bold;
    font-size: 1.1em;
}

.rating-hdr {
    display: flex;
    align-items: center;
    justify-content: center;
}

.reviews {
    display: grid;
    justify-content: center;
    grid-template-columns: repeat(auto-fill, minmax(400px, 450px));
    gap: 2em;
    padding: 2em 0;
}

.review-card {
    display: grid;
    grid-template-rows: repeat(3, min-content) 1fr min-content;
    max-width: 450px;
    min-height: 300px;
    border-radius: 20px;
    padding: 1em 1.5em;
    box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
}

.review-card p {
    line-break: anywhere;
    text-align: justify;
}

.posted-at {
    font-style: italic;
    align-self: end;
    color: #034298;
}

h5 {
    text-align: center;
}

h4 {
    text-align: center;
    margin: 0 0 1em 0;
}

.review-card >>> .v-btn--icon.v-btn--size-default {
    --v-btn-size: 1.5rem;
}

.v-rating {
    gap: 20px;

    margin-right: 20px;
}
.v-rating >>> * {
    cursor:unset;
}


</style>
