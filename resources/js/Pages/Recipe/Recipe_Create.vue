<template>
    <Head>
        <title>Create Recipe</title>
    </Head>

    <div class="form">
        <form @submit.prevent="submit">
            <div class="el">
                <div class="">
                    <label for="recipe_title">Recipe Title</label>
                    <input type="text" class="form-control" id="recipe_title" name="title" v-model="form.title"
                           placeholder="Enter recipe name">
                    <span class="text-danger text-center" v-if="$attrs.errors.title">
                                    {{ $attrs.errors.title }}
                </span>
                </div>
                <div class="optionals">
                    <div class="form-group" style="">
                        <v-checkbox-btn v-model="form.public" label="Public"></v-checkbox-btn>

                    </div>

                    <div class="form-group" style="">
                        <v-checkbox-btn v-model="form.favorite" label="Favorite"></v-checkbox-btn>

                    </div>

                </div>
            </div>


            <div class="el">
                <div class="">

                    <label for="ingredients">Ingredients</label>

                    <v-container fluid style="padding:0 !important;">
                        <v-row>
                            <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                <v-select
                                    id="ingredients"
                                    v-model="selectIngredients"
                                    :items="ingredients"
                                    item-title="name"
                                    item-value="id"
                                    label="Select ingredients"
                                    multiple
                                    chips
                                    closable-chips
                                    clearable
                                ></v-select>
                            </v-col>
                        </v-row>
                    </v-container>
                    <span class="text-danger text-center" v-if="$attrs.errors.ingredients">
                                    {{ $attrs.errors.ingredients }}
                                </span>
                </div>


                <div class="form-group">

                    <label for="categories">Categories</label>

                    <v-container fluid style="padding:0 !important;">
                        <v-row>
                            <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                <v-select
                                    id="categories"
                                    v-model="selectCategories"
                                    :items="categories"
                                    item-title="name"
                                    item-value="id"
                                    label="Select categories"
                                    multiple
                                    chips
                                    closable-chips
                                    clearable

                                ></v-select>
                            </v-col>
                        </v-row>
                    </v-container>
                    <span class="text-danger text-center" v-if="$attrs.errors.categories">
                                    {{ $attrs.errors.categories }}
                                </span>
                </div>
            </div>

            <div class="el">
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" maxlength="512" v-model="form.description"
                              placeholder="Write recipe description"></textarea>
                    <span class="text-danger text-center" v-if="$attrs.errors.description">
                                    {{ $attrs.errors.description }}
                                </span>
                </div>
                <div class="form-group">
                    <label for="instructions">Instructions</label>
                    <textarea class="form-control" id="instructions" maxlength="3000" v-model="form.instructions"
                              placeholder="Write recipe instructions"></textarea>
                    <span class="text-danger text-center" v-if="$attrs.errors.instructions">
                                    {{ $attrs.errors.instructions }}
                                </span>
                </div>
            </div>


            <button style="color:white;" type="submit" class="btn btn-primary" :disabled="form.processing">Create
                Recipe
            </button>
        </form>
    </div>

</template>

<script setup>
import {reactive} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import {ref} from 'vue';

const selectIngredients = ref([]);
const selectCategories = ref([]);


let form = reactive({
    title: '',
    description: '',
    instructions: '',
    ingredients: selectIngredients,
    categories: selectCategories,
    favorite: false,
    public: false,
});
/**
 * Submits the form data to the `/recipe` endpoint using an HTTP POST request.
 */
let submit = () => {
    Inertia.post('/recipe', form);
}


let {ingredients, categories} = defineProps(['ingredients', 'categories']);


</script>

<style scoped>
div.form {
    padding: 2em;

}

form {
    display: grid;
    gap: 1em;
}

textarea#description {
    min-height: 150px;
    max-height: 150px;
}

textarea#instructions {
    min-height: 150px;
    max-height: 500px;
}

form > button[type="submit"] {
    width: fit-content;
}

.v-checkbox-btn >>> label {
    font-size: 1.3rem;
    color: black;
}

.el {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 3em;

}

.optionals {
    display: flex;
    align-items: center;

    justify-content: center;
}
</style>
