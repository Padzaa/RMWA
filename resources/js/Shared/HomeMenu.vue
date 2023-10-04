<template>

    <div id="recipes_menu">
        <Link href="/favorites">
            <div class="card">
                <h2>Favorites</h2>
                <img

                    src="../../../public/heartmenu.svg/"
                    alt="picture">

            </div>
        </Link>

      <Link href="/like">
        <div class="card">
          <h2>Liked Recipes</h2>
          <img

              src="../../../public/likes.svg/"
              alt="picture">

        </div>
      </Link>

        <Link href="/recipe/create">
            <div class="card">
                <h2>Add Recipe</h2>
                <img
                    src="../../../public/addrecipe.svg"
                    alt="picture">

            </div>
        </Link>


        <Link href="/recipe">
            <div class="card">
                <h2>See All Recipes</h2>
                <img
                    src="../../../public/recipes.svg"
                    alt="picture">

            </div>
        </Link>

        <Link href="/review">
            <div class="card">
                <h2>My reviews</h2>
                <img
                    src="../../../public/review.svg"
                    alt="picture">

            </div>
        </Link>

      <Link href="/collection">
        <div class="card">
          <h2>My Collections</h2>
          <img
              src="../../../public/collections.svg"
              alt="picture">

        </div>
      </Link>

      <Link href="/follow">
        <div class="card">
          <h2>All follows</h2>
          <img
              src="../../../public/follow.svg"
              alt="picture">

        </div>
      </Link>
    </div>
</template>

<script>

import Card from "./Card.vue";

export default {
    name: "HomeMenu.vue",
    components: {}
}
</script>

<style scoped>
#recipes_menu {
    padding: 2em;
    display:flex;
    flex-wrap:wrap;
    gap: 1.5em;
    height: fit-content;
    justify-content:center;

}

h2 {
    text-align: center;
}

img {
    height: 100%;
    width: 100%;
}

div.card {
    height: 100%;
    padding: 1em;
    width:300px

}
</style>
