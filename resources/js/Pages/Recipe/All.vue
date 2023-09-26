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
                        <div class="dropdown-item">
                            <input class="input-cat" v-model="form.categories" type="checkbox" :value="category.id" :id="category.name"> <label :for="category.name">{{ category.name }}</label>
                        </div>
                    </template>

                </template>
            </Filter>

            <Filter>
                <template v-slot:button_name>Select Ingredients</template>
                <template v-slot:filter_slot>
                    <template v-for="ingredient in ingredients">
                        <div class="dropdown-item">
                            <input class="input-cat" v-model="form.ingredients" type="checkbox" :value="ingredient.id" :id="ingredient.name"> <label :for="ingredient.name">{{ ingredient.name }}</label>
                        </div>
                    </template>
                </template>
            </Filter>

            <div class="for-fav" style="">

                <input v-model="form.favorite" type="checkbox" class="btn-check" id="btn-check-outlined" autocomplete="off">
                <label class="btn btn-outline-primary" for="btn-check-outlined">Only Favorites</label>

            </div>
        </div>

            <button type="submit" name="submit" class="btn btn-primary">Filter Recipes</button>


    </form>
    <div class="grid-net">
        <Card v-for="(recipe,index) in recipes.data">
            <template v-slot:title>
                {{recipe.title}}
            </template>
            <template v-slot:instructions>
                {{recipe.instructions}} Lorem ipsum dolor sit amet con et element        nulla par
            </template>
            <template v-slot:actions>
                <div class="actions">
                    <Link class="btn btn-success"
                                           :href="recipes.path + '/' + recipe.id"
                                           method="GET"
                >Show</Link>
                    <Link class="btn btn-primary"
                          :href="recipes.path + '/' + recipe.id + '/edit'"
                          method="GET"
                    >Edit</Link>
                    <Link class="btn btn-danger"
                          :href="recipes.path + '/' + recipe.id"
                          method="DELETE"
                    >Delete</Link>
                    <Link class="heart" method="PUT" :href="'/recipe/' + recipe.id + '/favorite'" preserve-scroll>
                        <svg width="48px" height="48px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path :class="{'heart-svg': recipe.is_favorite }" d="M12 20C12 20 21 16 21 9.71405C21 6 18.9648 4 16.4543 4C15.2487 4 14.0925 4.49666 13.24 5.38071L12.7198 5.92016C12.3266 6.32798 11.6734 6.32798 11.2802 5.92016L10.76 5.38071C9.90749 4.49666 8.75128 4 7.54569 4C5 4 3 6 3 9.71405C3 16 12 20 12 20Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </Link>
                </div>
            </template>
        </Card>
    </div>



    <div id="paginator">
        <p>Recipes from {{recipes.from}} to {{recipes.to}} of total {{ recipes.total }}</p>
        Page:
        <template v-for="(link,index) in recipes.links">

            <Link v-if="index !== 0 && index !== (recipes.links.length - 1)"
                    :style="{
                'font-weight' : link.active ? 'bold' : 400,
                'color' : link.active ? 'red' : 'inherit'
                            }"
                  :href="link.url"

            >

                {{link.label }}
            </Link>

        </template>
    </div>
</template>

<script>

import {Link} from '@inertiajs/vue3';
import Card from '../../Shared/Card.vue';
import Filter from '../../Shared/Filter.vue';
import { Inertia } from '@inertiajs/inertia'


// Define a method to send the filter request



export default{
    props:{
        recipes:Array,
        categories:Object,
        ingredients:Object,

    },
    components:{
     Card,Link,Filter,
    },
    data(){
        return {
            form:{
                categories: [],
                ingredients: [],
                favorite:null,
            }
        }
    },
    methods:{
        submit(){
            Inertia.post('/recipe/filter',this.form);
        },

    }
}
</script>
<style scoped>
.grid-net{
    display:grid;
 grid-template-columns: repeat(auto-fill,minmax(330px,1fr));
    padding: 1em 2em;
    gap: 1em;
    grid-auto-rows: auto;
    justify-items: center;
}

p{
    font-style:italic;

    color: gray;
    text-align: center;
    margin-bottom:0;
}
#paginator{
    display: flex;
    gap:10px;
    justify-content:center;
    padding: 1em 0;
}
a{
    font-size:1.1rem;
    width:fit-content;
}
div.actions{
    align-self: end;
    display:flex;
    gap:10px;

}
.filter-form{
    display:grid;

}
.pickers{
    display:grid;
    grid-auto-flow: column;
    grid-template-columns: repeat(auto-fill,minmax(250px,1fr));
    width:85%;
    justify-self: center;
    place-items:center;
}
button[name="submit"]{
    width:85%;
    justify-self: center;
}
.for-fav{
    display:grid;
    place-items:center;
}
.for-fav > label{
    padding: 0.5em 1.6em;

}
</style>
