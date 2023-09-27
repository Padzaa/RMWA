<template>
    <Head>
        <title>Welcome</title>
    </Head>
    <Header/>

    <Recipes/>

</template>

<script>


import Recipes from "../Shared/Recipes.vue";

export default {
    components: {
        Recipes,
    },
    props: {},

}
</script>
