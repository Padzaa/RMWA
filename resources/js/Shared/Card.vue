<template>

    <v-card class="card">
        <div class="card-header">
            <slot name="title1"></slot>

        </div>
        <div class="card-body">
            <h5 class="card-title">
                <slot name="title"></slot>
            </h5>


            <b>Short Description:</b>

            <p class="card-text">
                <slot name="instructions">

                </slot>
            </p>
            <slot name="posted_at"></slot>

            <slot name="actions"></slot>


        </div>
    </v-card>


</template>

<script>


export default {
    name: "Card.vue",
    components: {}
}
</script>

<style scoped>
::-webkit-scrollbar {
    width: 7px;
}

/* Track */
::-webkit-scrollbar-track {
    background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #555;
}

div.card {
    min-width: 330px;
    max-width: 370px;
    width: 100%;
    max-height: 450px;
    min-height: 450px;
    display: grid;
    grid-template-rows: min-content 1fr;
    border-radius: 7px;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 1px 0px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 48px;
}

.card-header {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: center;
    background-color: orange;
}

.card-title {
    overflow: hidden;
    text-overflow: ellipsis !important;
    white-space: nowrap;
    font-size: 1.55rem;

}

.card-text {
    width: 100%;
    overflow-y: auto;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 5;
    display: -webkit-box;
    font-size: 1.1rem;
    padding-right: 10px;

    margin: 0;
//border-bottom: 1px solid white; color: #414141;
}

.card-body {
    display: grid;
    grid-template-rows: min-content min-content auto min-content 65px;
    padding: 15px 15px 8px 15px;
    background-color: #efefef;
    border-top: 1px solid #e5e5e5;

}
</style>
