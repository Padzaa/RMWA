<script>
import Header from "../../Shared/Header.vue";

export default {
  components: {Header},
  props: {
    collections: Object
  }
}
</script>

<template>

  <Head>
    <title>Collections</title>
  </Head>
  <div class="collections">
    <div class="collections-actions">
      <Link class="add-new btn" href="/collection/create">
        <img src="../../../../public/plus.svg" alt="">
      </Link>
    </div>
    <template v-for="collection in collections">
      <div class="single-collection">
        <div class="info">
          <h2>{{ collection.name }}</h2>
          <p>Number of recipes: {{ collection.recipes.length }}</p>

          <h4>Recipes:</h4>
          <template v-for="recipe in collection.recipes">
            <p class="titles">{{ recipe.title }}</p>
          </template>

          <div class="actions">
            <Link
                :href="'/collection/' + collection.id"
                method="GET">
              <img src="../../../../public/show.svg" alt="show">
            </Link>
            <Link v-if="this.$attrs.auth ? this.$attrs.auth.user.id === collection.user_id : false"
                  :href="'/collection/' + collection.id + '/edit'"
                  method="GET">
              <img src="../../../../public/edit.svg" alt="edit">
            </Link>
            <Link as="button" method="DELETE"
                  v-if="this.$attrs.auth ? this.$attrs.auth.user.id === collection.user_id : false"
                  :href="'/collection/' + collection.id">
              <img src="../../../../public/delete.svg" alt="delete">
            </Link>

          </div>

        </div>
      </div>
    </template>
  </div>


</template>

<style scoped>
.collections {
  padding: 2em;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5em
}

.single-collection {
  font-family: "Roboto", sans-serif;
  font-weight: 550;
  background-color: #f8f8f8;
  min-height: fit-content;
  padding: 1em;
  max-height: 600px;
  border-radius: 15px;
  box-shadow: rgba(17, 17, 26, 0.1) 0px 1px 0px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 48px;
}

div.actions {
  align-self: end;
  align-items: center;
  display: flex;
  gap: 17px;

}

div.actions > a {
  text-align: center;
  height: fit-content;
}

.collections-actions {
  grid-column: 1/-1;
}

.info {
  height: 100%;
  display: grid;
}

.info > h2 {
  text-transform: capitalize;
}
.info > p:first-child{
  color: gray;
}
.titles{
  color: #646464;
  font-style: oblique 20deg;
}
.add-new{
  background-color: #0080ff;

}
</style>
