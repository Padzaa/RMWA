<script >
export default {
    props:{
        reviews: Object
    }
}
</script>

<template>
        <div v-for="review in reviews">
            <h1>{{review.rating}}</h1>
            <h1>{{review.recipe()}}</h1>
            <h1>{{review.message}}</h1>
        </div>
</template>

<style scoped>

</style>
