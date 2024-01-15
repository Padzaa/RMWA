<script>
import {Inertia} from "@inertiajs/inertia";
import {capitalize} from "vue";

export default {
    props: {
        ingredients: {
            type: [Object, Array],
        },
        recipes: {
            type: [Object, Array],
        },
        title: String,
        currentLimit: Number,
        selectedIngredients: {
            type: [Object, Array,],
        },
    },
    methods: {
        capitalize,
        /**
         * Makes API call to get recipes based on ingredients
         */
        cook() {
            this.selected = this.selected.map((ingredient) => {
                return {
                    id: ingredient.id,
                    name: ingredient.name
                }
            });
            Inertia.get('/recipes/cooking', {
                requestedIngredients: JSON.stringify(this.selected),
                limit: this.selectedLimit
            }, {
                preserveScroll: true,
                only: ['recipes', 'currentLimit', 'selectedIngredients'],
            });
        },
        toggleCard(index) {
            this.show = this.show === index ? null : index;
        },
    },
    data() {
        return {
            selected: this.selectedIngredients,
            limits: [5, 10, 15, 20],
            selectedLimit: this.currentLimit || 5,
            show: null
        }
    },
}
</script>

<template>
    <Head :title="title"></Head>
    <div style="height: fit-content;padding: 0 1em 2em 1em;display: grid;
gap: 1em;">

        <form @submit.prevent="cook">
            <v-container fluid style="padding:0 !important;">
                <v-row>
                    <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                        <v-select
                            id="ingredients"
                            v-model="selected"
                            :items="ingredients"
                            return-object
                            item-title="name"
                            item-value="id"
                            name="ingredients"
                            label="Select ingredients"
                            multiple
                            chips
                            closable-chips
                            clearable
                        ></v-select>
                    </v-col>
                </v-row>
            </v-container>
            <v-select
                v-model="selectedLimit"
                :items="limits"
                label="Number of recipe ideas"
                name="limit"
            >
            </v-select>
            <v-btn type="submit" class="btn btn-primary">Cook</v-btn>
        </form>
        <p ref="limitReachedMessage"
           style="font-size: 15px;color: blue;font-style: italic;">
            You can get random recipes by not selecting any ingredients.
        </p>
        <p ref="limitReachedMessage" v-if="recipes.hasOwnProperty('code')"
           style="font-size: 14px;color: red;font-style: italic;">
            You have reached the limit.
        </p>
        <p ref="emptyRecipesMessage" v-if="recipes.hasOwnProperty('code') || recipes.length === 0"
           style="font-size: 24px;color: #8a8a8a;font-style: italic;text-align: center">
            No recipes found</p>
        <div class="cards">

            <v-card
                v-for="(recipe,index) in recipes.hasOwnProperty('code') ? [] : recipes.recipes ? recipes.recipes : recipes"
                class="mx-auto"
                :key="index"
                max-width="344"
                style="height: min-content;"
            >
                <v-img
                    :src="recipe.image"
                    height="200px"
                    cover
                ></v-img>

                <v-card-title>
                    {{ capitalize(recipe?.title) }}
                </v-card-title>

                <v-card-subtitle v-html="recipe.summary">

                </v-card-subtitle>

                <v-card-actions>
                    <v-btn
                        color="orange-lighten-2"
                        variant="text"
                        :href="recipe.sourceUrl"
                        target="_blank"
                    >
                        Explore
                    </v-btn>

                    <v-spacer></v-spacer>

                    <v-btn
                        :icon="show ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                        @click="toggleCard(index)"
                    ></v-btn>
                </v-card-actions>

                <v-expand-transition v-show="show === index">
                    <div>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div v-html="recipe.instructions"></div>
                        </v-card-text>
                    </div>
                </v-expand-transition>
            </v-card>

        </div>
    </div>
</template>

<style scoped>
.cards {
    display: grid;
    gap: 1em;
    grid-template-columns: repeat(auto-fit, minmax(344px, 1fr));
}

@media (max-width: 400px) {
    .cards {
        grid-template-columns: 1fr !important;
    }

    .cards:deep(.v-card) {
        width: 280px !important;
    }
}
</style>
