<script>
export default {
    name: "ADCards",
    props: {
        cards: {
            type: [Array, Object],
        }
    },
    methods: {
        /**
         * Pass data to dialog and opens it
         */
        openDialogAndPassData(title, data, fields) {
            this.$parent.isActive = true;
            this.$parent.dialogTitle = title;
            this.$parent.dialogData = data;
            this.$parent.dialogFields = fields;
        },
    }
}
</script>

<template>
    <div class="cards">
        <div v-for="card in cards" class="card">
            <div class="card-info">
                <p class="fst-italic text-grey-darken-1 fw-bold">{{ card.title }}</p>
                <h4 class="fw-bold">{{ card.value.length }}</h4>
            </div>
            <div class="icon-holder">
                <v-icon color="blue">{{ card.icon }}</v-icon>
            </div>
            <v-btn style="border-radius: 0 0 4px 4px" variant="flat"
                   @click="openDialogAndPassData(card.title, card.value, card.fields)"
                   class="read-more"
                    :disabled="card.value.length === 0">
                <v-icon>mdi-arrow-down</v-icon>
            </v-btn>
        </div>
    </div>
</template>

<style scoped>

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    grid-column: 1/-1;
}

.card {
    display: grid;
    grid-template-columns: 3fr 1fr;
    padding-top: 10px;
    row-gap: 10px;

}

.icon-holder > .v-icon {
    height: 100%;
    width: 100%;
    font-size: 2.6em;
}

.card-info > p {
    margin: 0;
}

.card-info {
    display: grid;
    gap: 20px;
    align-items: center;
    padding-left: 9px;

}

.read-more {
    grid-column: 1/-1;
    text-align: center;
    background-color: #e3e3e3;
}

.icon-holder {
    padding-right: 9px;
}

</style>
