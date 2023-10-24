<script>
import Header from "./Header.vue";
import Alert from "./Alert.vue";

export default {


    components: {
        Alert,
        Header
    },
    watch: {
        "$page.url": function (newUrl, oldUrl) {
            this.$page.url = newUrl;

        }
    }

}
</script>

<template>
    <Header :pageUrl="this.$page.url"/>
    <slot></slot>
    <Alert v-if="this.$attrs.alertFlash" :alertFlash="this.$attrs.alertFlash"/>
</template>


<style scoped>

</style>
