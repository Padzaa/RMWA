<script>
import Header from "../../Shared/Header.vue";

export default {
  components: {Header},
  props:{
    collections:Object
  }
}
</script>

<template>
  <Header></Header>
    <Head>
        <title>Collections</title>
    </Head>
  <div class="collections">
      <div class="collections-actions">
          <Link class="btn btn-primary" href="/collection/create">Create new collection</Link>
      </div>
    <template v-for="collection in collections">
    <div class="single-collection">
        <div class="info">
          <h2>{{collection.name}}</h2>
          <p>Number of recipes: {{collection.recipes.length}}</p>

            <h4>Recipes:</h4>
          <template v-for="recipe in collection.recipes">
              <p>{{recipe.title}}</p>
          </template>
          <div class="actions">
            <Link class="btn btn-success" :href="'/collection/' + collection.id">Show</Link>
            <Link class="btn btn-primary" :href="'/collection/' + collection.id + '/edit'">Edit</Link>
            <Link class="btn btn-danger" as="button" method="DELETE" :href="'/collection/' + collection.id">Delete</Link>
          </div>

        </div>
    </div>
    </template>
  </div>




</template>

<style scoped>
.collections{
  padding: 2em;
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap:1.5em
}
.single-collection {
    font-family: "Roboto", sans-serif;
    font-weight: 550;
  border:1px solid black;
  min-height: fit-content;
  padding: 1em;
  max-height: 600px;
  border-radius:15px;
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
.collections-actions{
    grid-column: 1/-1;
}
.info{
    height: 100%;
    display:grid;
}
</style>
