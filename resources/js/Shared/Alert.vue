<script>
export default {
    props: {
        alertFlash: Array,
    },
    data() {
        return {
            snackbar: true,
        }
    },
}
</script>

<template>
    <v-snackbar
        location="top right"
        timeout="5000"
        v-model="snackbar"
        top="20"
        right="20"
        vertical
        :color="alertFlash.type === 'error' ? 'error' : 'success'"
    >

        <div class="fs-4 pb-2">{{ alertFlash.title }}</div>
        <br>
        <p class="fs-6">{{ alertFlash.message }}</p>

        <template v-slot:actions>
            <v-btn
                color="white"
                append-icon="mdi-close"
                @click="snackbar = false"
            >
                Close
            </v-btn>
        </template>
    </v-snackbar>
</template>

<style>

</style>
