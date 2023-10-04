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
  <GridNet :recipes="likes" :auth="this.$attrs.auth"></GridNet>
</template>

<style scoped>

</style>
