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
        }
    },
    data() {
        return {
            form: {
                categories: this.filters.categories ? this.filters.categories : [],
                ingredients: this.filters.ingredients ? this.filters.ingredients : [],
                favorites: this.filters.favorites ? JSON.parse(this.filters.favorites) : null,
                ratings: this.filters.ratings ? this.filters.ratings : [],
                order: this.filters.order ? this.filters.order : 'desc',
                search: this.filters.search ? this.filters.search : '',
                collections: this.filters.collections ? this.filters.collections : [],
            },
            dialog: false,

        }
    },
}
</script>

<template>
    <div style="padding: 2em 4em;display:grid; justify-content: center">
    <v-btn style="width:fit-content;font-size:1.25rem;" @click="dialog = !dialog" color="black" append-icon="mdi-filter">Filter</v-btn>
    <v-row justify="center">
        <v-dialog
            v-model="dialog"
            width="fit-content"
        >

            <v-card style="border-radius:15px;min-height:350px;">
                <form @submit.prevent="submit" class="filter-form">
                    <div class="pickers" :style="!$page.props.auth ? 'grid-template-columns:repeat(2, 1fr);' : ''">
                        <Filter>
                            <template v-slot:button_name>Select Categories</template>
                            <template v-slot:filter_slot>
                                <template v-for="category in categories">
                                    <div class="dropdown-item" onclick="event.stopPropagation()">
                                        <input class="input-cat" v-model="form.categories" type="checkbox" :value="category.id"
                                               :id="category.name"> <label class="dr-lb" :for="category.name">{{
                                            category.name
                                        }}</label>
                                    </div>
                                </template>

                            </template>
                        </Filter>

                        <Filter v-if="$page.props.auth">
                            <template v-slot:button_name>Select Collections</template>
                            <template v-slot:filter_slot>
                                <template v-for="collection in collections">
                                    <div class="dropdown-item" onclick="event.stopPropagation()">
                                        <input class="input-cat" v-model="form.collections" type="checkbox" :value="collection.id"
                                               :id="collection.name"> <label class="dr-lb" :for="collection.name">{{
                                            collection.name
                                        }}</label>
                                    </div>
                                </template>

                            </template>
                        </Filter>


                        <Filter :style="!$page.props.auth ? 'grid-column: 2 / 3':''">
                            <template v-slot:button_name>Select Ingredients</template>
                            <template v-slot:filter_slot>
                                <template v-for="ingredient in ingredients">
                                    <div class="dropdown-item" onclick="event.stopPropagation()">
                                        <input class="input-cat" v-model="form.ingredients" type="checkbox" :value="ingredient.id"
                                               :id="ingredient.name"> <label class="dr-lb" :for="ingredient.name">{{
                                            ingredient.name
                                        }}</label>
                                    </div>
                                </template>
                            </template>
                        </Filter>

                        <div class="for-fav" v-if="$page.props.auth">

                            <v-checkbox-btn v-model="form.favorites" label="Favorite"></v-checkbox-btn>
                        </div>
                        <div class="rating" :style="!$page.props.auth ? 'grid-column: 1 / -1;grid-row:3;':''">
                            Rating:
                            <v-checkbox v-model="form.ratings" label="1-2" value="1"></v-checkbox>
                            <v-checkbox v-model="form.ratings" label="2-3" value="2"></v-checkbox>
                            <v-checkbox v-model="form.ratings" label="3-4" value="3"></v-checkbox>
                            <v-checkbox v-model="form.ratings" label="4-5" value="4"></v-checkbox>
                            <v-checkbox v-model="form.ratings" label="5" value="5"></v-checkbox>
                        </div>
                        <div class="order" :style="!$page.props.auth ? 'grid-column: 1 / -1;grid-row:2;':''">
                            Rating Order:
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="order" id="asc" value="asc" v-model="form.order">
                                <label class="form-check-label" for="asc"> <!--Rating-->
                                    <v-icon>mdi-sort-ascending</v-icon>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="order" id="desc" value="desc"
                                       v-model="form.order">
                                <label class="form-check-label" for="desc">

<!--                                    Rating-->
                                    <v-icon>mdi-sort-descending</v-icon>
                                </label>
                            </div>
                        </div>
                        <div class="search" :style="!$page.props.auth ? 'grid-column: 1 / -1;':''">

                            <input placeholder="Search recipes" v-model="form.search" type="search" name="search" id="search"
                                   class="form-input form-control">


                        </div>
                    </div>
                    <div class="filter-buttons">
                        <v-btn type="submit" name="submit" class="fr" color="blue" append-icon="mdi-filter">Filter Recipes </v-btn>
                        <v-btn @click="reset" name="reset" class="fr" color="red" append-icon="mdi-refresh">Reset Filters</v-btn>
                    </div>


                </form>
            </v-card>
        </v-dialog>
    </v-row>
    </div>
</template>

<style scoped>
.v-dialog >>> .v-card{
    display:grid !important;
}
.v-dialog >>> .v-card__underlay{
    display:none;
}
.filter-form {
    display: grid;
    padding: 2em 2em;
    height: 100% !important;
    grid-template-rows: 1fr min-content;
}

.pickers {
    display: grid;
    grid-template-columns: repeat(3,1fr);

    justify-self: center;
    place-items: center;
    padding: 0 2em 1.5em 2em;

    row-gap: 3em;
}


.filter-buttons {
    justify-self: center;
    display: grid;
    grid-auto-flow: column;
    gap: 1em;
    align-self:end;
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
    grid-column: 2/-1;
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
.search{
    grid-column: 1/-1;
    width: 100%;
    justify-self: end;
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

.order {
    display: flex;
    gap: 25px;
    font-size: 20px;
    grid-column: 1/-1;
}

.v-checkbox >>> .v-input__details {
    display: none !important;
}

</style>
