<template>

    <Head>
        <title>Edit Profile</title>
    </Head>
    <div class="container rounded bg-white mt-5 mb-5">
        <form enctype="multipart/form-data" @submit.prevent="submit">

        <div class="row justify-content-center" >
            <h2 class="text-center">Profile Settings</h2>

            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">

                        <label for="fileToUpload">
                            <div class="profile-pic" :style="background()">
                                <span>Change Image</span>
                            </div>
                        </label>
                        <input @change="onFileChange" type="File" name="fileToUpload" id="fileToUpload" @input="form.file = $event.target.files[0]" accept="image/jpg, image/jpeg, image/png">

                    <span class="font-weight-bold">{{ $page.props.auth.user.firstname }}</span>
                    <span class="text-black-50">{{ $page.props.auth.user.email }}</span>
                    <span> </span>
                </div>
            </div>
            <div class="col-md-5 border-right d-flex">
                <div class="p-3 py-5  align-self-center">

                    <div class="row mt-2">
                        <div class="col-md-6"><label class="labels">Firstname</label><input type="text"
                                                                                            name="firstname"
                                                                                            class="form-control"
                                                                                            placeholder="Firstname"
                                                                                            v-model="form.firstname">
                          <span class="text-danger text-center" v-if="$attrs.errors.firstname">
                                    {{$attrs.errors.firstname}}
                </span>
                        </div>
                        <div class="col-md-6"><label class="labels">Lastname</label><input type="text"
                                                                                           name="lastname"                                                                                           class="form-control"
                                                                                           v-model="form.lastname"

                                                                                           placeholder="Lastname">
                          <span class="text-danger text-center" v-if="$attrs.errors.lastname">
                                    {{$attrs.errors.lastname}}
                </span>
                        </div>

                    </div>
                    <div class="row mt-3">

                        <div class="col-md-12"><label class="labels">Email</label><input type="email"
                                                                                         name="email"
                                                                                         class="form-control"
                                                                                         placeholder="Enter email"
                                                                                         v-model="form.email"></div>
                      <span class="text-danger text-center" v-if="$attrs.errors.email">
                                    {{$attrs.errors.email}}
                </span>
                    </div>
                    <div class="mt-5 text-center">
                        <button @click="submit" class="btn btn-primary profile-button text-white" name="submit" type="submit">Save Profile</button>
                    </div>
                </div>
            </div>

        </div>
        </form>
    </div>
<div class="rec">
    <h1 class="text-center">My recipes</h1>
    <GridNet :recipes="recipes.data" :auth="this.$attrs.auth"></GridNet>
    <Paginator :recipes="recipes"></Paginator>
</div>


</template>

<script>
import { Inertia } from "@inertiajs/inertia";
import Card from '../../Shared/Card.vue';
import GridNet from "../../Shared/GridNet.vue";
import Paginator from "../../Shared/Paginator.vue";
export default {
    props: {
        recipes:Object
    },
    components: {
        Paginator,
        Card,GridNet
    },
    data() {
        return {
            form: {
                firstname: this.$page.props.auth.user.firstname,
                lastname: this.$page.props.auth.user.lastname,
                email: this.$page.props.auth.user.email,
                filename: "",
                file: null,
                _method: "put"
            },
            pic: this.$page.props.auth.user.picture ? this.$page.props.auth.user.picture : "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_640.png",
            url: "",


        }
    },
    mounted() {
        if (this.url) {
            this.url = this.url
        }else{
            this.url = this.pic
        }

    },
    methods:{
        close() {
            $('.modal').modal("hide");
        },
        onFileChange(e) {
            const file = e.target.files[0];

            this.form.filename = file.name;

            this.url = URL.createObjectURL(file);
        },
        background(){

            return {
                backgroundImage: `url('${this.url}')`
            };
        },
        submit(){
            const formData = new FormData();

            formData.append("firstname",this.form.firstname);
            formData.append("lastname",this.form.lastname);
            formData.append("email",this.form.email);
            formData.append("file",this.form.file);
            formData.append("filename",this.form.filename);
            formData.append("_method",this.form._method);

            Inertia.post('/user/'+this.$page.props.auth.user.id, formData,{
                headers: {
                    'Content-Type': 'multipart/form-data', // Important: Set the content type
                }
            });
        }
    }
}
</script>
<style scoped>
.profile-pic {
    border:5px solid red;
    border-radius: 50%;
    height: 150px;
    width: 150px;
    background-size: cover;
    background-position: center;
    background-blend-mode: multiply;
    vertical-align: middle;
    text-align: center;
    color: transparent;
    transition: all .3s ease;
    text-decoration: none;
    cursor: pointer;
    display:grid;
    align-items: center;
}

.profile-pic:hover {
    background-color: rgba(0,0,0,.5);
    z-index: 10000;
    color: #fff;
    transition: all .3s ease;
    text-decoration: none;
}

div.actions {
    align-self: end;
    align-items: center;
    display: flex;
    gap: 10px;

}
div.actions>a{
    text-align: center;
    height:fit-content;
}
.grid-net {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    padding: 1em 2em;
    gap: 1em;
    grid-auto-rows: auto;
    justify-items: center;
}

p {
    font-style: italic;

    color: gray;
    text-align: center;
    margin-bottom: 0;
}

form input[type="file"] {
    display: none;
    cursor: pointer;
}
.rec{
    padding: 0 2em;
}
</style>
