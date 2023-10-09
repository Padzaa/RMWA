<template>
    <Head>
        <title>Register</title>
    </Head>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2>Register</h2>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="submit">


                            <div class="row mb-3">
                                <label for="firstname" class="col-md-4 col-form-label text-md-end">Firstname</label>

                                <div class="col-md-6">
                                    <input v-model="form.firstname" id="firstname" type="text" class="form-control "
                                           name="firstname"  autocomplete="firstname" autofocus>

                                    <span class="text-danger text-center" v-if="$attrs.errors.firstname">
                                    {{$attrs.errors.firstname}}
                                </span>
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label for="lastname" class="col-md-4 col-form-label text-md-end">Lastname</label>

                                <div class="col-md-6">
                                    <input id="lastname" type="text" class="form-control" name="lastname"
                                           v-model="form.lastname"  autocomplete="lastname" autofocus>

                                    <span class="text-danger text-center" v-if="$attrs.errors.lastname">
                                    {{$attrs.errors.lastname}}
                                </span>
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" v-model="form.email"
                                           name="email"  autocomplete="email">

                                    <span class="text-danger text-center" v-if="$attrs.errors.email">
                                    {{$attrs.errors.email}}
                                </span>
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" v-model="form.password"
                                           name="password"  autocomplete="new-password">
                                    <span class="text-danger text-center" v-if="$attrs.errors.password">
                                    {{$attrs.errors.password}}
                                </span>
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Confirm
                                    Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                           v-model="form.password_confirmation" name="password_confirmation"
                                           autocomplete="new-password">
                                    <span class="text-danger text-center" v-if="$attrs.errors.password_confirmation">
                                    {{$attrs.errors.password_confirmation}}
                                </span>
                                </div>

                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4 gap-2 d-flex">
                                    <button type="submit" name="submit" :disabled="form.processing"
                                            class="btn btn-primary">
                                        Register
                                    </button>
                                    <Link class="btn btn-danger" href="/login">Login</Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script setup>
import {reactive} from 'vue';
import {Inertia} from '@inertiajs/inertia';

let form = reactive({
    firstname: "",
    lastname: "",
    email: "",
    password: "",
    password_confirmation: "",

});
let submit = () => {
    Inertia.post('/register', form);
}

</script>

<style scoped>
.container{
    height: 100%;
    padding:5em

}
button{
    color:white;
}
</style>
