<template>
    <Head>
        <title>Welcome</title>
    </Head>
    <Header/>

    <Recipes/>

</template>

<script>
import { Link } from "@inertiajs/vue3";

import Recipes from "../Shared/Recipes.vue";

export default{
    components:{

        Link,
        Recipes,
    },
    props:{

    },

}
</script>
