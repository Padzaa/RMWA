<template>
    <Header/>
    <Head>
        <title>{{recipe.title}}</title>
    </Head>
    <div class="show-recipe">
        <div class="recipe">
            <span>Created by: <Link class="owner" :href="'/user/' + recipe.user_id">{{recipe.user.firstname + " " + recipe.user.lastname}}</Link> </span>
            <Link v-if="this.$attrs.auth.user.id == recipe.user_id" class="heart" method="PUT" :href="'/recipe/' + recipe.id + '/favorite'" preserve-scroll>
                <svg width="48px" height="48px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path :class="{'heart-svg':recipe.is_favorite}"
                          d="M12 20C12 20 21 16 21 9.71405C21 6 18.9648 4 16.4543 4C15.2487 4 14.0925 4.49666 13.24 5.38071L12.7198 5.92016C12.3266 6.32798 11.6734 6.32798 11.2802 5.92016L10.76 5.38071C9.90749 4.49666 8.75128 4 7.54569 4C5 4 3 6 3 9.71405C3 16 12 20 12 20Z"
                          stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </Link>
            <h1>{{ recipe.title }}</h1>
            <h6>DESCRIPTION:</h6>
            <p class="description">{{ recipe.description }}</p>
            <h6>INGREDIENTS:</h6>
            <ul>
                <li v-for="ingredient in recipe.ingredients"><p>{{ ingredient.name }}</p></li>
            </ul>
            <h6>INSTRUCTIONS:</h6>
            <p class="instructions"> {{ recipe.instructions }}</p>
            <div class="rate-me">
                <h4>Average rating : {{average}}</h4>
                <div class="modals">
                  <Link class="btn" :href="'/recipe/' + recipe.id + '/like'" method="PUT" :class="is_liked ? 'btn-danger' : 'btn-primary'">{{ is_liked ? 'Dislike' : 'Like' }}</Link>
                    <button v-if="this.$attrs.auth.user.id == recipe.user_id" class="btn btn-primary share" data-bs-toggle="modal" :data-bs-target="'#shareModal'+recipe.id">
                        Share
                    </button>
                    <div v-if="this.$attrs.auth.user.id == recipe.user_id" class="modal fade" :id="'shareModal'+recipe.id" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="shareModalsLabel">Share with others</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <v-container fluid style="padding:0 !important;">
                                        <v-row>
                                            <v-col cols="12" style="padding:15px 12px 0 12px !important;">
                                                <v-combobox
                                                    id="share"
                                                    v-model="share.users"
                                                    :items="users"
                                                    item-title="firstname"
                                                    item-value="id"
                                                    label="Select users"
                                                    multiple

                                                ></v-combobox>
                                            </v-col>
                                        </v-row>
                                      <span class="text-danger text-center" v-if="$attrs.errors.users">
                                    {{$attrs.errors.users}}
                </span>
                                    </v-container>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button @click="submitShare" type="submit" class="btn btn-danger" id="shareRecipe">Share</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button"  class="btn btn-danger" data-bs-toggle="modal" :data-bs-target="'#exampleModal'+recipe.id">
                        Rate
                    </button>

                    <div class="modal fade" :id="'exampleModal'+recipe.id" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalsLabel">Rate This Recipe</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center">
                                        <v-rating style="justify-self: start;"
                                                                       v-model="rate.rating"
                                                                       bg-color="orange-lighten-1"
                                                                       color="blue"

                                    ></v-rating>
                                    </div>

                                    &nbsp;<span class="text-danger text-center" v-if="$attrs.errors.rating">
                                    {{$attrs.errors.rating}}
                </span>

                                    <textarea name="comment" class="form-control" id="comment" cols="30" rows="10" placeholder="Leave a comment" maxlength="500" v-model="rate.comment"></textarea>
                                  <span class="text-danger text-center" v-if="$attrs.errors.comment">
                                    {{$attrs.errors.comment}}
                </span>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button @click="submitReview"  type="submit" class="btn btn-danger" id="rate_recipe">Rate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="leave-comment">
          <label for="comment">Leave a comment:
          </label>
          <textarea name="comment" class="form-control" id="comment" placeholder="Leave a comment" maxlength="500" v-model="comment.ccomment"></textarea>
          <span class="text-danger text-center" v-if="$attrs.errors.ccomment">
                                    {{$attrs.errors.ccomment}}
                </span>
          <br>
          <button @click="submitComment" type="submit" class="btn btn-danger" id="comment_recipe" >Comment</button>
        </div>

        <div v-if="review" class="review alert alert-success" style="padding-top:0;">

            <div style="width:fit-content;display:grid;grid-template-columns:1fr 3fr;">
                <p style="height:fit-content;align-self:center;margin: 0;font-size:1.45rem">RATING:</p>
                <v-rating style="justify-self: start;"
                        v-model="review.rating"
                        bg-color="orange-lighten-1"
                        color="red"
                          disabled="true"
                ></v-rating>
            </div>
            <p>You rated this recipe with <b>{{review.rating}}</b> stars</p>
            <h6>COMMENT :</h6>
            <p class="comment">``{{review.message}}``</p>
        </div>

        <template v-for="review in reviews">
            <div class="review alert alert-danger">
                <h6>RATING: {{review.rating}}</h6>
                <div class="text-center">
                    <v-rating
                            v-model="review.rating"
                            bg-color="orange-lighten-1"
                            color="blue"
                    ></v-rating>
                </div>

                <h6>COMMENT :</h6>
                <p class="comment">``{{review.message}}``</p>
            </div>
        </template>
        <div class="comments">
            <template v-for="comment in comments">
                <div class="comment form-control">
                    <div class="picture">
                        <Link :href="'/user/' + comment.user_id">
                            <img :src="comment.user.picture ? comment.user.picture : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png'" alt="user"/>
                        </Link>
                    </div>
                    <div class="body">
                        <h6>{{comment.user.firstname + " " +comment.user.lastname}}</h6>
                        <p>{{comment.comment}}</p>
                    </div>
                </div>
            </template>
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
        review:Object,
        average:Number,
        reviews:Object,
        users:Object,
        shared_to:Object,
        comments:Object,
      is_liked:Boolean
    },
    components: {},
    methods:{
        submitReview() {
            $('.modal').modal("hide");
            Inertia.put('/recipe/'+this.recipe.id+'/rate', this.rate);
        },
        submitShare() {
            $('.modal').modal("hide");
            Inertia.put('/recipe/'+this.recipe.id+'/share', this.share);
        },
      submitComment() {


        Inertia.put('/recipe/'+this.recipe.id+'/comment', this.comment,{
          preserveScroll: true,
        });
          this.comment.ccomment = "";
      }
    },
    data() {
        return{
            rate:{
                rating: this.review ? this.review.rating : 0,
                comment: this.review ? this.review.message : "",
            },
            share: {
                users: this.shared_to ? this.shared_to : []
            },
            comment: {
                ccomment: ""
            },


        }
    }
}
</script>

<style scoped>

.owner{
    font-size:18px;
    font-style: italic;
}
.leave-comment{
  width:750px;
  display:grid;

}
div.comments{
  display:grid;
  gap:10px
}
div.comment{
  width:750px;
  display:grid;
  grid-template-columns: 1fr 9fr;

  padding:0 !important;

  border-radius:20px;
}
.body>h6{
  font-style: italic;
}
.body>p{
  font-size: 14px;
  color:gray;
  font-style: italic;
}
div.body{
  border-left: 1px solid lightgray;
  padding:1em;
}
div.picture{
  padding:1em;
}
.picture img{
 height:75px;
  width: 75px;
  border-radius: 50%;
  object-fit:cover;
}
.leave-comment>textarea{
  max-height: 150px;
  font-size:14px;
}
.review{
    min-width: 750px;
    max-width: 750px;
    height:fit-content !important;
    border-radius: 12px;
    margin: 0;
}
.show-recipe {
    display: grid;
    padding: 3em 0;
    justify-items: center;

    gap: 2em;
    grid-auto-rows: min-content;
}

.recipe {
    min-width: 750px;
    max-width: 750px;
    border: 1px solid gray;
    border-radius: 25px;
    padding: 1.3em 2em;
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
}

.instructions {
    font-size: 1.1rem;
    text-transform: capitalize;
    height: 100%;
}

.heart {
    position: absolute;
    top: 1.3em;
    right: 2em;
}
.rate-me{
    align-self:end;
    display:grid;
    grid-template-columns: 6fr 1fr;
    width: 100%;
}
.rate-me h4 {
    height: fit-content;
    align-self:center;
    margin:0;
    justify-self: start;
}
.modals{
    display: flex;
    gap:10px;
    justify-content:end;
}

</style>
