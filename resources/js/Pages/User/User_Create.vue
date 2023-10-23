<template>
    <Head>
        <title>Register</title>
    </Head>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div>
                            <v-card
                                class="mx-auto pa-12 pb-8"
                                elevation="8"
                                max-width="448"
                                rounded="lg"
                            >
                                <div class="text-subtitle-1 text-medium-emphasis">Firstname</div>

                                <v-text-field
                                    name="firstname"
                                    density="compact"
                                    placeholder="First name"
                                    prepend-inner-icon="mdi-account-outline"
                                    variant="outlined"
                                    v-model="form.firstname"

                                    :error-messages="$attrs.errors.firstname"

                                ></v-text-field>

                                <div class="text-subtitle-1 text-medium-emphasis">Lastname</div>

                                <v-text-field
                                    name="lastname"
                                    density="compact"
                                    placeholder="Last name"
                                    prepend-inner-icon="mdi-account-outline"
                                    variant="outlined"
                                    v-model="form.lastname"
                                    
                                    :error-messages="$attrs.errors.lastname"

                                ></v-text-field>

                                <div class="text-subtitle-1 text-medium-emphasis">Email</div>

                                <v-text-field
                                    name="email"
                                    density="compact"
                                    placeholder="Email address"
                                    prepend-inner-icon="mdi-email-outline"
                                    variant="outlined"
                                    v-model="form.email"
                                    type="email"
                                    :error-messages="$attrs.errors.email"

                                ></v-text-field>


                                <div
                                    class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                                    Password
                                </div>

                                <v-text-field
                                    name="password"
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

                                <div class="text-subtitle-1 text-medium-emphasis">Confirm password</div>

                                <v-text-field
                                    name="password_confirmation"
                                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                    :type="visible ? 'text' : 'password'"
                                    density="compact"
                                    placeholder="Confirm your password"
                                    prepend-inner-icon="mdi-lock-outline"
                                    variant="outlined"
                                    @click:append-inner="visible = !visible"
                                    v-model="form.password_confirmation"

                                ></v-text-field>


                                <v-btn
                                    block
                                    class="mb-8"
                                    color="blue"
                                    size="large"
                                    variant="tonal"
                                    :disabled="form.processing"
                                    type="submit"
                                >
                                    Sign up
                                </v-btn>

                                <v-card-text class="text-center">
                                    <a
                                        class="text-blue text-decoration-none"
                                        href="/login"
                                        rel="noopener noreferrer"

                                    >
                                        Already have an account?
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
