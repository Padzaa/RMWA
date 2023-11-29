<template>
    <form @submit.prevent="submit">
        <v-text-field label="Phone Number" type="phone" v-model="form.number" required></v-text-field>
        <v-text-field label="SMS" v-model="form.message" required></v-text-field>
        <v-btn type="submit" name="send">SEND</v-btn>
    </form>
</template>
<script>
import {Inertia} from "@inertiajs/inertia";

export default {
    props: {
        messages:{
            type: [Object, Array],
        },
    },
    name: "Sms",
    data() {
        return {
            form:{
                number: null,
                message: ""
            }
        }
    },
    methods: {
        submit(){
            Inertia.post("/sendsms",this.form);
        }
    }
}
</script>
<style scoped>

</style>
