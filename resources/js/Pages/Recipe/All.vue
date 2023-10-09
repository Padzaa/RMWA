<template>
    <Header/>
    <Head>
        <title>All Recipes</title>
    </Head>
    <form @submit.prevent="submit" class="filter-form">
        <div class="pickers">
            <Filter>
                <template v-slot:button_name>Select Categories</template>
                <template v-slot:filter_slot>
                    <template v-for="category in categories">
                        <div class="dropdown-item" onclick="event.stopPropagation()">
                            <input class="input-cat" v-model="form.categories" type="checkbox" :value="category.id"
                                   :id="category.name"> <label class="dr-lb" :for="category.name">{{ category.name }}</label>
                        </div>
                    </template>

                </template>
            </Filter>

            <Filter>
                <template v-slot:button_name>Select Collections</template>
                <template v-slot:filter_slot>
                    <template v-for="collection in collections">
                        <div class="dropdown-item" onclick="event.stopPropagation()">
                            <input class="input-cat" v-model="form.collections" type="checkbox" :value="collection.id"
                                   :id="collection.name"> <label class="dr-lb" :for="collection.name">{{ collection.name }}</label>
                        </div>
                    </template>

                </template>
            </Filter>


            <Filter>
                <template v-slot:button_name>Select Ingredients</template>
                <template v-slot:filter_slot>
                    <template v-for="ingredient in ingredients">
                        <div class="dropdown-item" onclick="event.stopPropagation()">
                            <input class="input-cat" v-model="form.ingredients" type="checkbox" :value="ingredient.id"
                                   :id="ingredient.name"> <label class="dr-lb" :for="ingredient.name">{{ ingredient.name }}</label>
                        </div>
                    </template>
                </template>
            </Filter>

            <div class="for-fav" style="">

                <input v-model="form.favorites" type="checkbox" class="btn-check" id="btn-check-outlined"
                       autocomplete="off">
                <label class="btn btn-outline-primary" for="btn-check-outlined" >Only Favorites</label>

            </div>
            <div class="rating">
                Rating:
                <div class="form-check">
                    <input class="form-check-input" v-model="form.ratings" name="rating" type="checkbox" value="1"
                           id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        1
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" v-model="form.ratings" name="rating" type="checkbox" value="2"
                           id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        2
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" v-model="form.ratings" name="rating" type="checkbox" value="3"
                           id="flexCheckDefault">
                    <label class="form-check-label" for="flexCheckDefault">
                        3
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" v-model="form.ratings" name="rating" type="checkbox" value="4"
                           id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        4
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" v-model="form.ratings" name="rating" type="checkbox" value="5"
                           id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        5
                    </label>
                </div>
            </div>
            <div class="order">
                <div class="form-check">
                <input class="form-check-input" type="radio" name="order" id="asc" value="asc" v-model="form.order">
                <label class="form-check-label" for="asc">Ascending Rating</label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="radio" name="order" id="desc" value="desc" v-model="form.order">
                <label class="form-check-label" for="desc">Descending Rating</label>
                </div>
            </div>
          <div class="search">

            <input placeholder="Search recipes" v-model="form.search" type="search" name="search" id="search" class="form-input form-control">


          </div>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Filter Recipes</button>


    </form>
    <GridNet :recipes="recipes.data" :auth="this.$attrs.auth"></GridNet>
    <div id="paginator">
        <p>Recipes from {{ recipes.from ? recipes.from : 0 }} to {{ recipes.to ? recipes.to : 0 }} of total {{ recipes.total }}</p>
        Page:
        <template v-for="(link,index) in recipes.links">

            <Link v-if="index !== 0 && index !== (recipes.links.length - 1)"
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

<script>
// <div className="modal fade" id="exampleModal52" tabIndex="-1" aria-labelledby="exampleModalLabel" data-v-ad55b4eb=""
//      aria-modal="true" role="dialog" aria-hidden="true" style="display: none;">

import Card from '../../Shared/Card.vue';
import Filter from '../../Shared/Filter.vue';
import {Inertia} from '@inertiajs/inertia'
import GridNet from "../../Shared/GridNet.vue";

export default {
    props: {
        recipes: Object,
        categories: Object,
        ingredients: Object,
        filterData: Object,
        rating: 0,
        collections:Object

    },

    components: {
        Card, Filter, GridNet
    },
    data() {
        return {
            form: {
                categories: this.filterData.categories ? this.filterData.categories : [],
                ingredients: this.filterData.ingredients ? this.filterData.ingredients : [],
                favorites: this.filterData.favorites ? this.filterData.favorites : null,
                ratings: this.filterData.ratings ? this.filterData.ratings : [],
                order: this.filterData.order ? this.filterData.order : 'desc',
                search: this.filterData.search ? this.filterData.search : '',
                collections: this.filterData.collections ? this.filterData.collections : [],
        },

    }
    },
    methods: {
        submit() {
            Inertia.get('/recipe', this.form);
        },


    },

}
</script>
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

.filter-form {
    display: grid;

}

.pickers {
   display: flex;
   flex-wrap: wrap;
    justify-content: center;
    width: 85%;
    justify-self: center;
    place-items: center;
    padding:1.5em 2em;
    column-gap:4em;
    row-gap: 2em;
}
.search{
  width:100%;
}
button[name="submit"] {
    width: 85%;
    justify-self: center;
}

.for-fav {
    display: grid;
    place-items: center;
}

.for-fav > label,
.rating > label {
    padding: 0.5em 1.6em;

}

.rating {
    display: flex;
    gap: 5px;
    font-size: 20px;
}

.btn-check:checked+.btn, .btn.active, .btn.show, .btn:first-child:active, :not(.btn-check)+.btn:active {
    color: var(--bs-btn-active-color);
    background-color: var(--bs-btn-active-bg);
    border-color: var(--bs-btn-active-border-color);
}
input#search{
  font-size:1.2em;
}
.dr-lb{
  width:calc(100% - 1px);
}

</style>
