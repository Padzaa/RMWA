<script>
import GridNet from "../../Shared/GridNet.vue";
export default {
  props: {
    recipes: Object,
      title:String
  },
  components:{
    GridNet
  }
}
</script>

<template>

<Head>
  <title>Shared Recipes</title>
</Head>
    <div class="c">
        <h1>{{title}}</h1>
        <GridNet :recipes="recipes" :auth="this.$attrs.auth"></GridNet>
    </div>

</template>

<style scoped>
.c{
    padding:2em ;
}
.c>h1{
    text-align: center;
}
</style>
