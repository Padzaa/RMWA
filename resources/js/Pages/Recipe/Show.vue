<template>

    <Head>
        <title>{{recipe.title}}</title>
    </Head>
    <div style="display:grid; justify-content: center;">
        <div class="show-recipe">
            <div class="recipe">
                <div class="show-header">
        <span>Created by <Link class="owner"
                               :href="'/user/' + recipe.user_id">{{
                recipe.user.firstname + " " + recipe.user.lastname
            }}</Link> </span>
                    <Link v-if="this.$attrs.auth ? this.$attrs.auth.user.id == recipe.user_id : false" class="heart"
                          method="PUT"
                          :href="'/recipe/' + recipe.id + '/favorite'" preserve-scroll>
                        <svg width="48px" height="48px" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path :class="{'heart-svg': recipe.is_favorite }"
                                  d="M12 20C12 20 21 16 21 9.71405C21 6 18.9648 4 16.4543 4C15.2487 4 14.0925 4.49666 13.24 5.38071L12.7198 5.92016C12.3266 6.32798 11.6734 6.32798 11.2802 5.92016L10.76 5.38071C9.90749 4.49666 8.75128 4 7.54569 4C5 4 3 6 3 9.71405C3 16 12 20 12 20Z"
                                  stroke="gray" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </Link>
                </div>
                <div class="show-body">
                    <h1>{{ recipe.title }}</h1>
                    <div class="description-div">
                        <h6>DESCRIPTION:</h6>
                        <p class="description">{{ recipe.description }}</p>
                    </div>
                    <div class="ingredients-div">
                        <h6>INGREDIENTS:</h6>
                        <ul>
                            <li v-for="ingredient in recipe.ingredients">
                                <p>{{ ingredient.name }}</p>
                            </li>
                        </ul>
                    </div>
                    <div class="instructions-div">
                        <h6>INSTRUCTIONS:</h6>
                        <p class="instructions"> {{ recipe.instructions }}</p>
                    </div>
                    <div class="rate-me">
                        <h4>Average rating : {{ average }}</h4>
                        <div class="modals">
                            <Link v-if="this.$attrs.auth" :href="'/recipe/' + recipe.id + '/like'" method="PUT"
                            >
                                <img v-if="!is_liked" src="../../../../public/like.svg" alt="Unlike">
                                <img v-if="is_liked" src="../../../../public/dislike.svg" alt="Like">

                            </Link>
                            <button v-if="this.$attrs.auth ? this.$attrs.auth.user.id == recipe.user_id : false"
                                    class=" share"
                                    data-bs-toggle="modal"
                                    :data-bs-target="'#shareModal'+recipe.id">
                                <img src="../../../../public/share.svg" alt="">
                            </button>
                            <div v-if=" this.$attrs.auth ? this.$attrs.auth.user.id == recipe.user_id : false"
                                 class="modal fade" :id="'shareModal'+recipe.id" tabindex="-1"
                                 aria-labelledby="shareModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="shareModalsLabel">Share with others</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">

                                            <v-container fluid style="padding:0 !important;">
                                                <v-row>
                                                    <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                                        <v-select
                                                            id="share"
                                                            v-model="share.users"
                                                            :items="users"
                                                            item-title="firstname"
                                                            item-value="id"
                                                            label="Select users"
                                                            multiple="true"
                                                            clearable="true"
                                                            chips="true"
                                                            closable-chips="true"
                                                        ></v-select>
                                                    </v-col>
                                                </v-row>

                                            </v-container>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary text-white"
                                                    data-bs-dismiss="modal">Cancel
                                            </button>
                                            <button @click="submitShare" type="submit" class="btn btn-danger text-white"
                                                    id="shareRecipe">Share
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button v-if="this.$attrs.auth" type="button" data-bs-toggle="modal"
                                    :data-bs-target="'#exampleModal'+recipe.id">
                                <img src="../../../../public/rate.svg" alt="">
                            </button>

                            <div class="modal fade" :id="'exampleModal'+recipe.id" tabindex="-1"
                                 aria-labelledby="exampleModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalsLabel">Rate This Recipe</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center">
                                                <v-rating style="justify-self: start;"
                                                          v-model="rate.rating"
                                                          bg-color="orange-lighten-1"
                                                          color="blue"
                                                          required
                                                ></v-rating>
                                            </div>

                                            &nbsp;

                                            <textarea name="comment" class="form-control" id="comment" cols="30"
                                                      rows="10"
                                                      placeholder="Leave message" maxlength="500" required
                                                      v-model="rate.msg"></textarea>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary text-white"
                                                    data-bs-dismiss="modal">Cancel
                                            </button>
                                            <button @click="submitReview" type="submit"
                                                    class="btn btn-danger text-white"
                                                    id="rate_recipe">Rate
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div v-if="this.$attrs.auth" class="leave-comment">
                <label for="comment">Leave a comment:
                </label>

                <div class="textarea-container">
                    <v-textarea
                        counter
                        :rules="[comment.comment.length <= 500 || 'Character limit exceeded']"
                        append-inner-icon="mdi"
                        class="mx-2 vutext"
                        label="Leave your comment"
                        rows="1"
                        v-model="comment.comment"
                        required

                    ></v-textarea>
                    <button @click="submitComment" type="submit" class="submit-button">
                        <v-icon>mdi-send</v-icon>
                    </button>
                </div>

                <span class="text-danger text-center" v-if="$attrs.errors.comment">
            {{ $attrs.errors.comment }}
        </span>


            </div>

            <div class="reviews">
                <div v-if="review" class="review alert alert-success" style="padding-top:0;">

                    <div style="display:grid;">

                        <v-rating style="justify-self: center;"
                                  v-model="review.rating"
                                  bg-color="orange-lighten-1"
                                  color="red"
                                  disabled="true"
                        ></v-rating>
                    </div>
                    <p style="font-size: 14px;text-align: center">You rated this recipe with <b>{{ review.rating }}</b>
                        stars</p>

                    <p style="text-align: justify;" class="comment"><q>{{ review.message }}</q></p>
                </div>
                <template v-for="review in reviews">
                    <div class="review alert alert-danger">
                        <h6> {{ review.rating }}</h6>
                        <div class="text-center">
                            <v-rating
                                v-model="review.rating"
                                bg-color="orange-lighten-1"
                                color="blue"
                            ></v-rating>
                        </div>

                        <h6>COMMENT :</h6>
                        <p class="comment"><q>{{ review.message }}</q></p>
                    </div>
                </template>
            </div>


            <div class="comments">
                <template v-for="comment in comments">
                    <div class="comment form-control">
                        <div class="picture">
                            <Link :href="'/user/' + comment.user_id">
                                <img
                                    :src="comment.user.picture ? comment.user.picture : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png'"
                                    alt="user"/>
                            </Link>
                        </div>
                        <div class="body">
                            <h6>{{ comment.user.firstname + " " + comment.user.lastname }}</h6>
                            <p>{{ comment.comment }}</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</template>

<script>

import {Inertia} from "@inertiajs/inertia";


export default {
    name: "Show.vue",
    props: {
        recipe: Object,
        ingredients: Array,
        review: Object,
        average: Number,
        reviews: Object,
        users: Object,
        shared_to: Object,
        comments: Object,
        is_liked: Boolean
    },
    components: {},

    methods: {
        /**
         * Submits a review.
         */
        submitReview() {
            $('.modal').modal("hide");
            Inertia.put('/recipe/' + this.recipe.id + '/rate', this.rate);
        },
        /**
         * Submit the share.
         */
        submitShare() {
            $('.modal').modal("hide");
            Inertia.put('/recipe/' + this.recipe.id + '/share', this.share);
        },
        /**
         * Submits a comment for a recipe.
         */
        submitComment() {
            Inertia.put('/recipe/' + this.recipe.id + '/comment', this.comment, {
                preserveScroll: true,
            });
            this.comment.comment = "";
        }

    },
    data() {
        return {
            rate: {
                rating: this.review ? this.review.rating : 0,
                msg: this.review ? this.review.message : "",
            },
            share: {
                users: this.shared_to ? this.shared_to : []
            },
            comment: {
                comment: ""
            },


        }
    }
}
</script>

<style scoped>
.description-div,
.ingredients-div {
    min-height: 100px;
}

.instructions-div {
    min-height: 250px;
}

.textarea-container {
    position: relative;
}

.textarea-container >>> textarea {

}

.submit-button {
    font-size: 20px;
    position: absolute;
    right: 0;
    top: calc(50% - 27px);
    padding-right: 7px;
}

.owner {
    font-size: 18px;
    font-style: italic;
}

.leave-comment {
    width: 750px;
    display: grid;

}

div.comments {
    display: grid;
    gap: 10px
}

div.comment {
    width: 750px;
    display: grid;
    grid-template-columns: 1fr 9fr;

    padding: 0 !important;

    border-radius: 20px;
}

.body > h6 {
    font-style: italic;
}

.body > p {
    font-size: 14px;
    color: gray;
    font-style: italic;
}

div.body {
    border-left: 1px solid lightgray;
    padding: 1em;
}

div.picture {
    padding: 1em;
}

.picture img {
    height: 75px;
    width: 75px;
    border-radius: 50%;
    object-fit: cover;
}

.leave-comment > textarea {
    max-height: 150px;
    font-size: 14px;
}

.review {
    width: 100%;
    height: fit-content !important;
    border-radius: 12px;
    margin: 0;
}

.show-recipe {
    display: grid;
    padding: 3em 0;
    justify-items: center;
    width: 750px;
    gap: 2em;
    grid-auto-rows: min-content;
}

.recipe {
    min-width: 750px;
    max-width: 750px;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px;
    border-radius: 25px;
    padding: 10px 20px 20px 20px;
    position: relative;
    display: flex;
    flex-direction: column;


}

.description {
    font-style: italic;
    text-transform: capitalize;
    font-size: 1.2rem;
    color: gray;
}

h1 {
    text-transform: capitalize;
    text-align: center;
    margin: 0.3em 0 0.5em 0;
}

.instructions {
    font-size: 1.27rem;

    height: 100%;
    max-width: 65ch;
    color: #2a2828;
}

.rate-me {
    align-self: end;
    display: grid;
    grid-template-columns: 6fr 1fr;
    width: 100%;
}

.rate-me h4 {
    height: fit-content;
    align-self: center;
    margin: 0;
    justify-self: start;
    font-style: italic;
}

.modals {
    display: flex;
    gap: 18px;
    justify-content: end;
}

.show-header {
    display: grid;
    grid-template-columns: auto min-content;
}

.show-header > span {
    height: fit-content;
    align-self: center;
}

.show-body {
    display: grid;
    gap: 15px;
}

.ingredients-div ul p {
    margin: 0 0 5px 0;
}

.show-body h6 {
    font-size: 1.35rem;
}

.vutext >>> * {
    max-height: 150px;
}

.v-textarea {
    margin: 0 !important;
}

.vutext >>> .v-field {
    padding: 0 !important;
}

.vutext >>> .v-field__append-inner {
    cursor: pointer;
    padding: 0 12px !important;
}

.review .v-rating >>> * {
    cursor: unset;
}

.reviews {
    display: grid;
    width: 100%;
    gap: 2em;
    grid-template-columns: repeat(2, 1fr);
}
</style>
