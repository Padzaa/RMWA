<template>
    <Head>
        <title>Create Recipe</title>
    </Head>
    <Header/>
    <div class="form">
        <form @submit.prevent="submit">
            <div class="form-group">
                <label for="recipe_title">Recipe Title</label>
                <input type="text" class="form-control" id="recipe_title" name="title" v-model="form.title" placeholder="Enter recipe name" required>
                <span class="text-danger text-center" v-if="$attrs.errors.title">
                                    {{$attrs.errors.title}}
                </span>
            </div>
            <div class="form-group">

                <label for="ingredients">Ingredients</label>

<!--                            <select id="ingredients" class="form-select">-->
<!--                                <option selected>Select a favorite activity or create a new one</option>-->

<!--                                    <option v-for="(ingredient,index) in ingredients" :value="ingredient.id" class="ingredient">{{ingredient.name}}</option>-->

<!--                            </select>-->
                <v-container fluid style="padding:0 !important;">
                    <v-row>
                <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                    <v-combobox
                        id="ingredients"
                        v-model="selectIngredients"
                        :items="ingredients"
                        item-title="name"
                        item-value="id"
                        label="Select ingredients"
                        multiple

                    ></v-combobox>
                </v-col>
                    </v-row>
                </v-container>
                <span class="text-danger text-center" v-if="$attrs.errors.ingredients">
                                    {{$attrs.errors.ingredients}}
                                </span>
            </div>
            <div class="form-group">

                <label for="categories">Categories</label>

                <!--                            <select id="ingredients" class="form-select">-->
                <!--                                <option selected>Select a favorite activity or create a new one</option>-->

                <!--                                    <option v-for="(ingredient,index) in ingredients" :value="ingredient.id" class="ingredient">{{ingredient.name}}</option>-->

                <!--                            </select>-->
                <v-container fluid style="padding:0 !important;">
                    <v-row>
                        <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                            <v-combobox
                                id="categories"
                                v-model="selectCategories"
                                :items="categories"
                                item-title="name"
                                item-value="id"
                                label="Select categories"
                                multiple

                            ></v-combobox>
                        </v-col>
                    </v-row>
                </v-container>
                <span class="text-danger text-center" v-if="$attrs.errors.categories">
                                    {{$attrs.errors.categories}}
                                </span>
            </div>
            <div class="form-group d-grid" style="">
                <label >Favorite</label>
                <input v-model="form.favorite" type="checkbox" class="btn-check" id="btn-check-outlined" autocomplete="off">
                <label class="btn btn-outline-primary" for="btn-check-outlined">Make This Recipe Your Favorite</label><br>

            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" maxlength="512" v-model="form.description" placeholder="Write recipe description" required></textarea>
                <span class="text-danger text-center" v-if="$attrs.errors.description">
                                    {{$attrs.errors.description}}
                                </span>
            </div>
            <div class="form-group">
                <label for="instructions">Instructions</label>
                <textarea class="form-control" id="instructions" maxlength="3000" v-model="form.instructions" placeholder="Write recipe instructions" required></textarea>
                <span class="text-danger text-center" v-if="$attrs.errors.instructions">
                                    {{$attrs.errors.instructions}}
                                </span>
            </div>

            <button type="submit" class="btn btn-primary" :disabled="form.processing">Create Recipe</button>
        </form>
    </div>

</template>

<script setup>
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { ref } from 'vue';

const selectIngredients = ref([]);
const selectCategories = ref([]);


let form = reactive({
    title:'',
    description: '',
    instructions: '',
    ingredients: selectIngredients,
    categories: selectCategories,
    favorite: false,
});
let submit = () =>{
    Inertia.post('/recipe',form);
}
let { ingredients,categories } = defineProps(['ingredients','categories']);


</script>

<style scoped>
div.form{
    padding: 2em;

}
form{
    display: grid;
    gap: 1em;
}
textarea#description{
    min-height:150px;
    max-height:150px;
}
textarea#instructions{
    min-height:150px;
    max-height:500px;
}
</style>
