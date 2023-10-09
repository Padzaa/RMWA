<script>
import Card from "./Card.vue";
import {Inertia} from "@inertiajs/inertia";

export default{
    methods: {
      submit() {
        $('.modal').modal("hide");

      },
    },
    props: {
        recipes: {
            type: [Object, Array],
        },
        auth: Object
    },
    components:{
        Card,
    }
}

</script>

<template>
    <div class="grid-net">
        <p v-if="recipes.length == 0 && this.$page.component !== 'Recipe/All'" class="records text-center">0 records found</p>
        <Card v-for="(recipe,index) in recipes">
            <template v-slot:title1>

                <h5><i>Rating: </i><b>{{ recipe.average_rating ? recipe.average_rating : 0 }}</b> </h5>based on {{recipe.review_count ? recipe.review_count : 0}} reviews
            </template>
            <template v-slot:title>
                {{recipe.title}}
            </template>
            <template v-slot:instructions>
                {{ recipe.instructions }}
            </template>
            <template v-slot:actions>
                <div class="actions">
                    <Link class="btn btn-success"
                          :href="'/recipe/' + recipe.id"
                          method="GET"
                    >Show
                    </Link>
                    <Link v-if="auth.user.id == recipe.user_id" class="btn btn-primary"
                          :href="'/recipe/' + recipe.id + '/edit'"
                          method="GET"
                    >Edit
                    </Link>
                    <button v-if="auth.user.id == recipe.user_id" type="button" class="btn btn-danger"
                            data-bs-toggle="modal" :data-bs-target="'#exampleModal'+recipe.id">
                        Delete
                    </button>
                    <div class="modal fade" :id="'exampleModal'+recipe.id" tabindex="-1"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalsLabel">Confirm your deletion</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this recipe permanently?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                                    </button>
                                    <Link @click="submit" method="DELETE" :href="'/recipe/' + recipe.id"
                                          class="btn btn-danger" id="delete_recipe">Delete Recipe
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Link v-if="auth.user.id == recipe.user_id" class="heart" method="PUT"
                          :href="'/recipe/' + recipe.id + '/favorite'" preserve-scroll>
                        <svg width="48px" height="48px" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path :class="{'heart-svg': recipe.is_favorite }"
                                  d="M12 20C12 20 21 16 21 9.71405C21 6 18.9648 4 16.4543 4C15.2487 4 14.0925 4.49666 13.24 5.38071L12.7198 5.92016C12.3266 6.32798 11.6734 6.32798 11.2802 5.92016L10.76 5.38071C9.90749 4.49666 8.75128 4 7.54569 4C5 4 3 6 3 9.71405C3 16 12 20 12 20Z"
                                  stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </Link>
                </div>
            </template>
        </Card>
    </div>
</template>

<style scoped>
.grid-net {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    padding: 1em 2em;
    gap: 1em;
    grid-auto-rows: auto;
    justify-items: center;
}

p {
    font-style: italic;

    color: gray;
    text-align: center;
    margin-bottom: 0;
}

div.actions {
    align-self: end;
    align-items: center;
    display: flex;
    gap: 10px;

}

div.actions > a {
    text-align: center;
    height: fit-content;
}
h5{
    text-align:center;
    margin:0;
}
.records{
    grid-column: 2/3;

    font-size: 1.65rem;
}
</style>
