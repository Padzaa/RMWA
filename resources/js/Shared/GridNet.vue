<script>
import Card from "./Card.vue";
import {Inertia} from "@inertiajs/inertia";

export default{
    methods: {
      submit() {
        $('.modal').modal("hide");

      }
    },
    props: {
        recipes: {
            type: [Object, Array],
        },
        auth: Object
    },
    components:{
        Card,
    },
    created(){
        this.recipes.forEach(recipe => {
            recipe.dialog = false;
        })
    },

}

</script>

<template>
    <p v-if="recipes.length == 0 && this.$page.component !== 'Recipe/All'" class="records text-center">0 records found</p>
    <div class="grid-net">

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
                <v-card-actions class="actions">
                    <Link
                          :href="'/recipe/' + recipe.id"
                          method="GET">
                        <img src="../../../public/show.svg" alt="show">
                    </Link>
                    <Link v-if="auth ? auth.user.id == recipe.user_id : false"
                          :href="'/recipe/' + recipe.id + '/edit'"
                          method="GET">
                        <img src="../../../public/edit.svg" alt="edit">
                    </Link>
                    <button v-if="auth ? auth.user.id == recipe.user_id : false"
                           @click="recipe.dialog = true;">
                        <img src="../../../public/delete.svg" alt="delete">
                    </button>
                    <v-dialog v-model="recipe.dialog" class="vd" style="display: grid">
                        <div class="bg-white dialog">

                                <div class="dialog-header">
                                    <h1 class="modal-title fs-4" >Confirm your deletion</h1>
                                    <button type="button" class="btn-close"  @click="recipe.dialog = false"
                                            aria-label="Close"></button>

                                </div>
                                <div class="dialog-message">
                                    Are you sure you want to delete recipe <b>"{{ recipe.title }}"</b> permanently?
                                </div>
                                <div class="dialog-actions">
                                    <button type="button" class="btn btn-outline-secondary" @click="recipe.dialog = false">Cancel
                                    </button>
                                    <Link @click="submit" method="DELETE" :href="'/recipe/' + recipe.id"
                                          class="btn btn-outline-danger" id="delete_recipe">Delete Recipe
                                    </Link>
                                </div>

                        </div>
                    </v-dialog>

                    <Link v-if="auth ? auth.user.id == recipe.user_id : false" class="heart" method="PUT"
                          :href="'/recipe/' + recipe.id + '/favorite'" preserve-scroll>
                        <svg width="48px" height="48px" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path :class="{'heart-svg': recipe.is_favorite }"
                                  d="M12 20C12 20 21 16 21 9.71405C21 6 18.9648 4 16.4543 4C15.2487 4 14.0925 4.49666 13.24 5.38071L12.7198 5.92016C12.3266 6.32798 11.6734 6.32798 11.2802 5.92016L10.76 5.38071C9.90749 4.49666 8.75128 4 7.54569 4C5 4 3 6 3 9.71405C3 16 12 20 12 20Z"
                                  stroke="gray" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </Link>
                </v-card-actions>

            </template>

        </Card>
    </div>

</template>

<style scoped>
.grid-net {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    padding: 2em 4em;
    gap: 2.5em;
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
    display: grid;
    gap: 25px;
    grid-template-columns: min-content min-content min-content 1fr ;
    height: 65px;
}
div.actions>button, div.actions>a {
    font-size: 0.9rem;
    color:black;

}


h5{
    text-align:center;
    margin:0;
}
.records {
    grid-column: 2/3;

    font-size: 1.65rem;
}
.vd{
    display: grid;
    align-items: start;
    justify-content: center;
}
.vd > div{
    align-items: center ;
}
.dialog{
    display: grid;
    padding: 1em;
    border-radius: 15px;
    width:fit-content;
    align-self:center;
    gap: 1em;

}
.dialog-header{
    display: grid;
    grid-template-columns: 5fr min-content;
}
.dialog-message{
    font-size: 1.1rem;
}
.dialog-actions{
    display: grid;
    gap: 1em;
    grid-auto-flow: column;
}
.heart{
    justify-self:end;
}

</style>
