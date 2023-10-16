<script>
export default {
  props: {
    recipes: Object,
  }
}
</script>

<template>
    <div id="paginator">
        <p>Recipes from {{ recipes.from ? recipes.from : 0 }} to {{ recipes.to ? recipes.to : 0 }} of total {{
                recipes.total
            }}</p>
        Page:
        <template v-for="(link,index) in recipes.links">

            <Link v-if="index !== 0 && index !== (recipes.links.length - 1)"
                  :style="{
                      'font-weight' : link.active ? 'bold' : 400,
                      'color' : link.active ? 'red' : 'inherit'
                      }"
                  :href="link.url"

            >

                {{ link.label }}
            </Link>

        </template>
    </div>
</template>

<style scoped>
#paginator {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding: 1em 0;
}
p {
    font-style: italic;

    color: gray;
    text-align: center;
    margin-bottom: 0;
}
</style>
