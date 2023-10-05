<script>
import GridNet from "../../Shared/GridNet.vue";
export default {
  props: {
    recipes: Object
  },
  components:{
    GridNet
  }
}
</script>

<template>
<Header></Header>
<Head>
  <title>Shared Recipes</title>
</Head>
  <GridNet :recipes="recipes" :auth="this.$attrs.auth"></GridNet>
</template>

<style scoped>

</style>
