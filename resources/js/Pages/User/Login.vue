<template>
    <Head>
        <title>Login</title>
    </Head>
    <div class="container">
        <div class="row justify-content-center d-flex">
            <div class="col-md-8 form">


                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div>
                            <v-card
                                class="mx-auto pa-12 pb-8"
                                elevation="8"
                                max-width="448"
                                rounded="lg"
                            >
                                <div class="text-subtitle-1 text-medium-emphasis">Account</div>

                                <v-text-field
                                    density="compact"
                                    placeholder="Email address"
                                    prepend-inner-icon="mdi-email-outline"
                                    variant="outlined"
                                    v-model="form.email"
                                    type="email"
                                    :error-messages="$attrs.errors.email"
                                    autocomplete="email"

                                ></v-text-field>

                                <div
                                    class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                                    Password
                                </div>

                                <v-text-field
                                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                    :type="visible ? 'text' : 'password'"
                                    density="compact"
                                    placeholder="Enter your password"
                                    prepend-inner-icon="mdi-lock-outline"
                                    variant="outlined"
                                    @click:append-inner="visible = !visible"
                                    v-model="form.password"
                                    :error-messages="$attrs.errors.password"
                                ></v-text-field>
                                    <span class="text-danger text-center" style="display:block;" v-if="$attrs.errors.failedToLogin">
                                        {{$attrs.errors.failedToLogin}}
                                    </span>
                                <v-card
                                    class="mb-12 mt-8"
                                    color="surface-variant"
                                    variant="tonal"
                                >
                                    <v-card-text class="text-medium-emphasis text-caption">
                                        Warning: After 5 consecutive failed login attempts, you account will be
                                        temporarily suspended for <b>30 seconds</b>.
                                    </v-card-text>
                                </v-card>

                                <v-btn
                                    block
                                    class="mb-8"
                                    color="blue"
                                    size="large"
                                    variant="tonal"
                                    :disabled="form.processing"
                                    type="submit"
                                >
                                    Log In
                                </v-btn>

                                <v-card-text class="text-center">
                                    <a
                                        class="text-blue text-decoration-none"
                                        href="/register"
                                        rel="noopener noreferrer"

                                    >
                                        Sign up now
                                        <v-icon icon="mdi-chevron-right"></v-icon>
                                    </a>
                                </v-card-text>
                            </v-card>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</template>
<script setup>
import {reactive, ref} from 'vue';
import {Inertia} from '@inertiajs/inertia';

let visible = ref(false);

let form = reactive({
    email: "",
    password: "",

});
let submit = () => {
    Inertia.post('/login', form);
}

</script>

<style scoped>
button {
    color: white;
}

.container {

    padding: 5em

}

</style>
