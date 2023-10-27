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
    methods: {
        /**
         * Submit the filter form.
         *
         * @return {void}
         */
        submit() {
            let url = this.$page.url.includes('/recipe') ? '/recipe' : '/public';
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
                order: 'desc',
                search: '',
                collections: [],
            };
            this.submit();
        },
        /**
         * Checks if the search field is empty and submits the form if it is.
         */
        ifSearchEmpty() {
            if (this.form.search === "") {
                this.submit();
            }
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
            },
            dialog: false,
            order:[
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

    }
}
</script>

<template>
    <div style="padding: 2em 4em 1em 4em;display:grid;grid-auto-flow:column;grid-template-columns: 1fr 2fr;">
        <div class="search" style="display: grid;grid-auto-flow:column;gap:5px;">
            <input placeholder="Search recipes" @keyup="ifSearchEmpty" v-model="form.search" type="search" name="search"
                   id="search"
                   class="form-input form-control">

            <v-btn @click="submit" style="width:10px;height: 100%;">
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

                <v-card style="border-radius:15px;min-height:400px;">
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
                                            multiple=true
                                            chips=true
                                            closable-chips
                                            clearable=true

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
                                            v-model="form.collections"
                                            :items="collections"
                                            item-title="name"
                                            item-value="id"
                                            label="Select collections"
                                            multiple=true
                                            chips=true
                                            closable-chips
                                            clearable=true

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
                                            multiple=true
                                            chips=true
                                            closable-chips
                                            clearable=true

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
                            <div class="rating">
                                Rating:
                                <v-checkbox v-model="form.ratings" label="1+" value="1"></v-checkbox>
                                <v-checkbox v-model="form.ratings" label="2+" value="2"></v-checkbox>
                                <v-checkbox v-model="form.ratings" label="3+" value="3"></v-checkbox>
                                <v-checkbox v-model="form.ratings" label="4+" value="4"></v-checkbox>
                                <v-checkbox v-model="form.ratings" label="5" value="5"></v-checkbox>
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
}

.pickers {
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

    display: flex;
    gap: 5px;
    align-items: center;
    font-size: 20px;
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
