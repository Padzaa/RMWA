<template>

    <div id="recipes_menu">
            <div class="recipes">
                <h1>Recipes</h1>
                <div class="recipes-actions">
                    <Link><v-list-item prepend-icon="mdi-book-open-variant">Create new recipe</v-list-item></Link>
                    <Link><v-list-item prepend-icon="mdi-book-open-variant">List of all recipes</v-list-item></Link>
                    <Link><v-list-item prepend-icon="mdi-heart">My favorites</v-list-item></Link>
                    <Link><v-list-item prepend-icon="mdi-thumb-up">Liked recipes</v-list-item></Link>
                    <Link><v-list-item prepend-icon="mdi-share">Shared recipes</v-list-item></Link>
                    <Link><v-list-item prepend-icon="mdi-reply">Recipes shared with me</v-list-item></Link>

                </div>
            </div>

        <div class="collection">
            <h1>Collection</h1>
            <div class="recipes-actions">
                <Link><v-list-item prepend-icon="mdi-bag-personal-plus">Create new collection</v-list-item></Link>
                <Link><v-list-item prepend-icon="mdi-bag-personal">My collections</v-list-item></Link>


            </div>

        </div>

    </div>
</template>

<script>


export default {
    name: "HomeMenu.vue",
    // data() {
    //     return {
    //         links: [
    //             {
    //                 link: "/favorites",
    //                 title: "Favorites",
    //                 icon: "/heartmenu.svg"
    //             },
    //             {
    //                 link: "/like",
    //                 title: "Liked Recipes",
    //                 icon: "/likes.svg"
    //             },
    //             {
    //                 link: "/recipe/create",
    //                 title: "Add Recipe",
    //                 icon: "/addrecipe.svg"
    //             },
    //             {
    //                 link: "/recipe",
    //                 title: "See All Recipes",
    //                 icon: "/recipes.svg"
    //             },
    //             {
    //                 link: "/review",
    //                 title: "My reviews",
    //                 icon: "/review.svg"
    //             },
    //             {
    //                 link: "/collection",
    //                 title: "My Collections",
    //                 icon: "/collections.svg"
    //             },
    //             {
    //                 link: "/follow",
    //                 title: "All followers",
    //                 icon: "/follow.svg"
    //             },
    //             {
    //                 link: "/shared",
    //                 title: "I Shared",
    //                 icon: "/shared.svg"
    //             },
    //             {
    //                 link: "/sharedwithme",
    //                 title: "Shared With Me",
    //                 icon: "/shared.svg"
    //             }
    //         ]
    //     }
    // }
}
</script>

<style scoped>
#recipes_menu {
    padding: 2em;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5em;
    height: fit-content;


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
    padding: 1em 1.5em;
    width: 300px;

}
</style>
