<script >
import Header from "../../Shared/Header.vue";

export default {
  components: {Header},
    props:{
        reviews: Object,

    },
    data(){
        return {
            rating: 0,
        }
    }
}
</script>

<template>
  <Header></Header>
  <Head>
    <title>Reviews
    </title>
  </Head>
  <div class="container">
    <h1>Reviews</h1>
    <template v-for="review in reviews.data">
      <div class="alert alert-success">
        <h1>Rating: {{review.rating}} </h1>
        <h3>Recipe Name: <Link class="recipe_name" :href="'/recipe/'+review.recipe.id">{{review.recipe.title}}</Link> </h3>
        <h5>Review Message: "{{review.message}}"</h5>
      </div>

    </template>
  </div>

  <div id="paginator">
    <p>Recipes from {{ reviews.from ? reviews.from : 0 }} to {{ reviews.to ? reviews.to : 0 }} of total {{ reviews.total }}</p>
    Page:
    <template v-for="(link,index) in reviews.links">

      <Link v-if="index !== 0 && index !== (reviews.links.length - 1)"
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

<style scoped>
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
.recipe_name{
  font-weight: bold;
  font-size: 1.1em;
}
h5{
  font-style: italic;
}
.container{
  padding: 2em 0;
}
</style>
