<template>
    <Head>
        <title>Login</title>
    </Head>
    <div class="container">
        <div class="row justify-content-center d-flex">
            <div class="col-md-8 form">
                <div class="card">
                    <div class="card-header"><h2>Login</h2></div>

                    <div class="card-body">
                        <form @submit.prevent="submit">

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control " name="email"
                                           v-model="form.email" required autocomplete="email" autofocus>

                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" v-model="form.password"
                                           name="password" required autocomplete="current-password">

                                </div>
                                <span class="text-danger text-center" v-if="$attrs.errors.error">
                                    {{$attrs.errors.error}}
                                </span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">

                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4 gap-2 d-flex">
                                    <button type="submit" name="submit" :disabled="form.processing"
                                            class="btn btn-primary">
                                        Login
                                    </button>
                                    <Link class="btn btn-danger" href="/register">Register</Link>


                                    <!--                                    @if (Route::has('password.request'))-->
                                    <!--                                    <a class="btn btn-link" href="{{ route('password.request') }}">-->
                                    <!--                                        {{ __('Forgot Your Password?') }}-->
                                    <!--                                    </a>-->
                                    <!--                                    @endif-->
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
    email: "",
    password: "",

});
let submit = () => {
    Inertia.post('/login', form);
}

</script>

<style scoped>
button{
    color:white;
}
.container{
    height: 100%;
    padding:5em

}

</style>
