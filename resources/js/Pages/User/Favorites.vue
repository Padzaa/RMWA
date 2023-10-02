<script>
    import Header from "../../Shared/Header.vue";
    import GridNet from "../../Shared/GridNet.vue";
    export default {
        components: {GridNet, Header},
        props:{
            recipes:Array
        },
        data(){

        }
    }
</script>

<template>
    <Header />
    <Head>
        <title>Favorites</title>
    </Head>

    <GridNet :recipes="recipes" :auth="this.$attrs.auth"></GridNet>

</template>

<style scoped>
.grid-net {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    padding: 1em 2em;
    gap: 1em;
    grid-auto-rows: auto;
    justify-items: center;
}

p {
    font-style: italic;

    color: gray;
    text-align: center;
    margin-bottom: 0;
}
div.actions {
    align-self: end;
    align-items: center;
    display: flex;
    gap: 10px;

}
div.actions>a{
    text-align: center;
    height:fit-content;
}
</style>
