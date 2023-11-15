<script>
import Filter from "./Filter.vue";
import {Inertia} from "@inertiajs/inertia";

export default {
    props: {
        categories: Object,
        collections: Object,
        recipes: Object,
        ingredients: Object,
        filters: Object,
    },
    watch: {
        "form.r_from"(newVal) {
            this.adjustValues();
        },
        "form.r_to"(newVal) {
            this.adjustValues();
        }
    },
    methods: {
        /**
         * Submit the filter form.
         *
         * @return {void}
         */
        submit() {
            let url = this.$page.url.includes('/recipe') ? '/recipe' : '/guest/public';
            Inertia.get(url, this.form);
        },
        /**
         * Reset the filter form.
         *
         * @return {void}
         */
        reset() {
            this.form = {
                categories: [],
                ingredients: [],
                favorites: null,
                ratings: [],
                order: '',
                search: '',
                collections: [],
            };
            this.submit();
        },
        /**
         * Checks if the search field is empty or if Enter is pressed and submits the form if it is.
         */
        ifSearchEmpty(e) {
            if (this.form.search === "" || e.key === "Enter") {
                this.submit();
            }
        },
        /**
         * Adjusting values for ratings filters
         */
        adjustValues() {

            let from = this.form.r_from;//Selected rating from
            let to = this.form.r_to;//Selected rating to
            if(from > 5 || to > 5){
                from = 1;
                to = 5;
            }
            let ratings_to = [1, 2, 3, 4, 5];
            let ratings_from = [1, 2, 3, 4, 5];
            if (from != null) {
                ratings_to = ratings_to.filter(number => number > from);
            }
            if (to != null) {
                ratings_from = ratings_from.filter(number => number < to);
            }
            this.form.r_to = to;
            this.form.r_from = from;
            this.ratings_to = ratings_to;
            this.ratings_from = ratings_from;
        }
    },
    data() {
        return {
            form: {
                categories: [],
                ingredients: [],
                favorites: this.filters.favorites ? JSON.parse(this.filters.favorites) : null,
                ratings: this.filters.ratings ? this.filters.ratings : [],
                order: this.filters.order ? this.filters.order : null,
                search: this.filters.search ? this.filters.search : '',
                collections: [],
                r_from: this.filters.r_from ?? null,
                r_to: this.filters.r_to ?? null,
            },
            ratings_from: [
                1, 2, 3, 4, 5
            ],
            ratings_to: [
                1, 2, 3, 4, 5
            ],

            dialog: false,
            order: [
                {
                    name: "Created At Desc",
                    value: "created_at-desc"
                },
                {
                    name: "Created At Asc",
                    value: "created_at-asc"
                },
                {
                    name: "Rating Desc",
                    value: "average_rating-desc"
                },
                {
                    name: "Rating Asc",
                    value: "average_rating-asc"
                },
            ]

        }
    },
    mounted() {
        if (this.filters.categories) {
            this.filters.categories.forEach((el) => {
                el = parseInt(el);
                this.form.categories.push(el);
            });

        }
        if (this.filters.ingredients) {
            this.filters.ingredients.forEach((el) => {
                el = parseInt(el);
                this.form.ingredients.push(el);
            });

        }
        if (this.filters.collections) {
            this.filters.collections.forEach((el) => {
                el = parseInt(el);
                this.form.collections.push(el);
            });

        }
        this.adjustValues();

    }
}
</script>

<template>
    <div style="padding: 2em 4em 1em 4em;display:grid;grid-auto-flow:column;grid-template-columns: 1fr 2fr;">
        <div class="search" style="display: grid;grid-auto-flow:column;gap:5px;">
            <input placeholder="Search recipes" @keyup="ifSearchEmpty" v-model="form.search" type="search" name="search"
                   id="search"
                   class="form-input form-control">

            <v-btn @click="submit" style="width:10px;height: 100%;" type="submit">
                <v-icon style="font-size: 2em">mdi-magnify</v-icon>
            </v-btn>


        </div>
        <div style="justify-self: end;display:grid;grid-auto-flow:column;gap:10px;">
            <v-btn style="width:fit-content;font-size:1.25rem;justify-self:end;" @click="dialog = !dialog" color="black"
                   append-icon="mdi-filter">Filter
            </v-btn>
            <v-btn @click="reset">
                <v-icon style="font-size: 2em">mdi-refresh</v-icon>
            </v-btn>
        </div>

        <v-row justify="center">
            <v-dialog
                v-model="dialog"
                width="fit-content"
            >

                <v-card style="border-radius:15px;min-height:400px;min-width:350px;">
                    <form @submit.prevent="submit" class="filter-form">
                        <div class="pickers">
                            <v-container fluid style="padding:0 !important;">
                                <v-row>
                                    <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                        <v-select
                                            id="ingredients"
                                            v-model="form.categories"
                                            :items="categories"
                                            item-title="name"
                                            item-value="id"
                                            label="Select categories"
                                            multiple
                                            chips
                                            closable-chips
                                            clearable

                                        >

                                        </v-select>
                                    </v-col>
                                </v-row>
                            </v-container>

                            <v-container v-if="this.$page.props.auth" fluid style="padding:0 !important;">
                                <v-row>
                                    <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                        <v-select
                                            id="ingredients"
                                            v-model="form.collections"
                                            :items="collections"
                                            item-title="name"
                                            item-value="id"
                                            label="Select collections"
                                            multiple
                                            chips
                                            closable-chips
                                            clearable

                                        >

                                        </v-select>
                                    </v-col>
                                </v-row>
                            </v-container>


                            <v-container fluid style="padding:0 !important;">
                                <v-row>
                                    <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                        <v-select
                                            id="ingredients"
                                            v-model="form.ingredients"
                                            :items="ingredients"
                                            item-title="name"
                                            item-value="id"
                                            label="Select ingredients"
                                            multiple
                                            chips
                                            closable-chips
                                            clearable

                                        >

                                        </v-select>
                                    </v-col>
                                </v-row>
                            </v-container>

                            <v-container fluid style="padding:0 !important;">
                                <v-row>
                                    <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                        <v-select
                                            id="order"
                                            v-model="form.order"
                                            :items="order"
                                            item-title="name"
                                            item-value="value"
                                            label="Order by">
                                        </v-select>
                                    </v-col>
                                </v-row>
                            </v-container>
                            <div class="for-fav" v-if="$page.props.auth">
                                <v-checkbox-btn v-model="form.favorites" label="Favorite"></v-checkbox-btn>
                            </div>
                            <div class="rating" style="width: 100%">

                                <v-select
                                    label="Rating from"
                                    v-model="form.r_from"
                                    :items="ratings_from"
                                    item-title="ratings_from"
                                    item-value="ratings_from"
                                >
                                </v-select>

                                <v-select
                                    label="Rating to"
                                    v-model="form.r_to"
                                    :items="ratings_to"
                                    item-title="ratings_to"
                                    item-value="ratings_to"
                                >
                                </v-select>
                            </div>


                        </div>
                        <div class="filter-buttons">
                            <v-btn type="submit" name="submit" class="fr" color="blue" append-icon="mdi-filter">Filter
                            </v-btn>
                        </div>


                    </form>
                </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<style scoped>

.v-dialog >>> .v-card {
    display: grid !important;
}

.v-dialog >>> .v-card__underlay {
    display: none;
}

.filter-form {
    display: grid;
    padding: 2em 2em;
    height: 100% !important;
    grid-template-rows: 1fr min-content;
    width: 100%;
}

.pickers {
    width: 100%;
    display: grid;
    justify-self: center;
    place-items: center;
    padding: 0 0 1.5em 0;
    grid-auto-rows: min-content;
    gap: 2em;

}


.filter-buttons {
    justify-self: center;
    display: grid;
    grid-auto-flow: column;
    gap: 1em;
    align-self: end;
}

.for-fav {
    display: grid;
    place-items: center;
}

.v-checkbox-btn >>> label, .v-checkbox >>> label {
    font-size: 1.25rem;
    color: black;

}

.rating {

    display: grid;
    gap: 20px;
    align-items: center;
    font-size: 20px;
    grid-template-columns: 1fr 1fr;
}

.btn-check:checked + .btn, .btn.active, .btn.show, .btn:first-child:active, :not(.btn-check) + .btn:active {
    color: var(--bs-btn-active-color);
    background-color: var(--bs-btn-active-bg);
    border-color: var(--bs-btn-active-border-color);
}

input#search {
    font-size: 1.2em;
}

.dr-lb {
    width: calc(100% - 1px);
}

button.fr {
    color: white;
    font-size: 1.1em;
}


.v-checkbox >>> .v-input__details {
    display: none !important;
}

.v-container >>> .v-field__input {
    max-width: 400px;


}

</style>
