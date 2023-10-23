<script>
import {Inertia} from '@inertiajs/inertia';

export default {
  props: {
    recipes: Object,
  },
  data() {
    return {
      form: {
        name: "",
        recipes: [],
      }
    }
  },
  methods: {
    submit() {
      Inertia.post('/collection', this.form);
    }
  }
}


</script>

<template>

  <Head>
    <title>Create new Collection</title>
  </Head>

  <div class="form">
    <form @submit.prevent="submit">
      <div class="form-group">
        <label for="collection_name">Collection Title</label>
        <input type="text" class="form-control" id="collection_name" name="name" v-model="form.name"
               placeholder="Enter collection name" required>
        <span class="text-danger text-center" v-if="$attrs.errors.name">
                                    {{ $attrs.errors.name }}
                </span>
      </div>
      <div class="form-group">

        <label for="ingredients">Recipes</label>

        <v-container fluid style="padding:0 !important;">
          <v-row>
            <v-col cols="12" style="padding:15px 12px 0 12px !important;">
              <v-select
                  id="recipes"
                  v-model="form.recipes"
                  :items="this.recipes"
                  item-title="title"
                  item-value="id"
                  label="Select recipes"
                  multiple="true"
                  clearable="true"
              ></v-select>
            </v-col>
          </v-row>
        </v-container>
        <span class="text-danger text-center" v-if="$attrs.errors.recipes">
                                    {{ $attrs.errors.recipes }}
                                </span>
      </div>
      <button type="submit" class="btn btn-primary text-white" :disabled="form.processing">Create new Collection
      </button>

    </form>
  </div>

</template>

<style scoped>


div.form {
  padding: 2em;

}

form {
  display: grid;
  gap: 1em;
}

form > button {
  width: fit-content;
}
</style>
