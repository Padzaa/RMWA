<script >

import GridNet from "../../Shared/GridNet.vue";

export default {
  props: {
    likes:Object
  },
  components:{
    GridNet,
  }
}
</script>

<template>
<Header></Header>
  <Head>
    <title>Liked Recipes</title>
  </Head>
    <div class="container">
        <h1 class="text-center">Liked recipes</h1>
        <GridNet :recipes="likes" :auth="this.$attrs.auth"></GridNet>
    </div>


</template>

<style scoped>
.container{
    padding: 2em 0;
}
</style>
