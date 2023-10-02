<template>

    <div class="card bg-light mb-3" style="max-width: 18rem;">
        <div class="card-header">
            <slot name="title1"></slot>

        </div>
        <div class="card-body">
            <h5 class="card-title">
                <slot name="title"></slot>
            </h5>
            <p class="card-text">
                <slot name="instructions"></slot>
            </p>
            <slot name="actions"></slot>
        </div>
    </div>


</template>

<script>


export default {
    name: "Card.vue",
    components: {}
}
</script>

<style scoped>
div.card {
    min-width: 330px;
    max-height: 450px;
    min-height: 450px;
}

.card-header {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
   text-align: center;
}
.card-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

}
.card-text {
    width: 100%;

    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 5;
    display: -webkit-box;


}

.card-body {
    display: grid;
    height: 100%;
    padding-bottom:10px;
    grid-template-rows: min-content auto min-content;
}
</style>
