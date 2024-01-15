<script>
import Card from "./Card.vue";
import {Inertia} from "@inertiajs/inertia";

export default {
    methods: {
        /**
         * Submits the form and hides the modal.
         */
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
    components: {
        Card,
    },
    data() {
        return {
            dialog: false,
            dialogData: [],
        }
    },


}

</script>

<template>

    <div class="grid-net" v-if="recipes.length > 0">

        <Card v-for="(recipe,index) in recipes">
            <template v-slot:title1>

                <h5><i>Rating: </i><b>{{ recipe.average_rating ? recipe.average_rating : 0 }}</b></h5>based on
                {{ recipe.review_count ? recipe.review_count : 0 }} reviews
            </template>

            <template v-slot:title>
                {{ recipe.title }}
            </template>

            <template v-slot:instructions>

                <span>{{ recipe.description }}</span>
            </template>

            <template v-slot:posted_at>
                <span style="font-style: italic;">Posted at: {{ this.$utils.normalDate(recipe.created_at) }}</span>
            </template>

            <template v-slot:actions>
                <v-card-actions class="actions">
                    <Link
                        :href="auth ? '/recipe/' + recipe.id : '/guest/recipe/' + recipe.id"
                        method="GET">
                        <v-icon class="icon-of-rcard">mdi-eye-outline</v-icon>
                    </Link>
                    <Link v-if="auth ? auth.user.id == recipe.user_id : false"
                          :href="'/recipe/' + recipe.id + '/edit'"
                          method="GET">
                        <v-icon class="icon-of-rcard">mdi-pencil-outline</v-icon>
                    </Link>
                    <button v-if="auth ? auth.user.id == recipe.user_id : false"
                            @click="[dialogData = recipe,dialog = true]">
                        <v-icon class="icon-of-rcard">mdi-delete-outline</v-icon>
                    </button>
                    <Link v-if="auth ? auth.user.id == recipe.user_id : false" as="button" class="heart" method="PUT"
                          :href="'/recipe/' + recipe.id + '/favorite'" preserve-scroll>
                        <v-icon class="icon-of-rcard">{{
                                recipe.is_favorite ? 'mdi-heart' : 'mdi-heart-outline'
                            }}
                        </v-icon>
                    </Link>
                </v-card-actions>

            </template>

        </Card>
        <v-dialog v-model="dialog" class="vd" style="display: grid">
            <div class="bg-white dialog">

                <div class="dialog-header">
                    <h1 class="modal-title fs-4">Confirm your deletion</h1>
                    <button type="button" class="btn-close" @click="dialog = false"
                            aria-label="Close"></button>

                </div>
                <div class="dialog-message">
                    Are you sure you want to delete recipe <b>"{{ dialogData.title }}"</b> permanently?
                </div>
                <div class="dialog-actions">
                    <button type="button" class="btn btn-outline-secondary" @click="dialog = false">
                        Cancel
                    </button>
                    <Link @click="submit" method="DELETE" :href="'/recipe/' + dialogData.id"
                          class="btn btn-outline-danger" id="delete_recipe">Delete Recipe
                    </Link>
                </div>

            </div>
        </v-dialog>

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
    grid-template-columns: min-content min-content min-content 1fr;
    height: 65px;
}

div.actions > button, div.actions > a {
    font-size: 0.9rem;
    color: black;

}


h5 {
    text-align: center;
    margin: 0;
}

.records {
    grid-column: 2/3;

    font-size: 1.65rem;
}

.vd {
    display: grid;
    align-items: start;
    justify-content: center;
}

.vd > div {
    align-items: center;
}

.dialog {
    display: grid;
    padding: 1em;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    width: fit-content;
    align-self: center;
    gap: 1em;

}

.dialog-header {
    display: grid;
    grid-template-columns: 5fr min-content;
}

.dialog-message {
    font-size: 1.1rem;
}

.dialog-actions {
    display: grid;
    gap: 1em;
    grid-auto-flow: column;
}

.heart {
    justify-self: end;
}

.icon-of-rcard {
    font-size: 42px;
    color: #d91432;
}

@media (max-width: 500px) {
    .grid-net {
        padding: 1em !important;
        grid-template-columns: unset !important;
    }
}
</style>
