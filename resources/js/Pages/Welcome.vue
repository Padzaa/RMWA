<template>
    <Head>
        <title>Welcome</title>
    </Head>
    <Header/>

    <HomeMenu/>

</template>

<script>


import HomeMenu from "../Shared/HomeMenu.vue";

export default {
    components: {
        HomeMenu,
    },
    props: {},

}
</script>
