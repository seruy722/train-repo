<template>
    <v-app id="inspire">
        <v-container fluid fill-height>
            <v-layout align-center justify-center>
                <v-flex xs12 sm8 md4>
                    <v-card class="elevation-12">
                        <v-toolbar dark color="primary">
                            <v-toolbar-title>Регистрация</v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-img
                                :src="`/logo.png`"
                                :lazy-src="`https://picsum.photos/10/6?image=${n * 5 + 10}`"
                                aspect-ratio="1.7"
                                contain
                            >
                                <v-layout
                                    slot="placeholder"
                                    fill-height
                                    align-center
                                    justify-center
                                    ma-0
                                >
                                    <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                                </v-layout>
                            </v-img>
                        </v-toolbar>
                        <v-card-text>
                            <v-form>
                                <v-text-field v-model="data.name" prepend-icon="person_pin" name="name" label="Имя"
                                              type="text" :error-messages="checkError('name')"></v-text-field>
                                <v-text-field v-model="data.email" prepend-icon="person" name="email" label="E-mail"
                                              type="text" :error-messages="checkError('email')"></v-text-field>
                                <v-text-field v-model="data.password" id="password" prepend-icon="lock" name="password"
                                              label="Пароль" type="password" :error-messages="checkError('password')">
                                </v-text-field>
                                <v-text-field v-model="data.password_confirmation" id="confirm_password"
                                              prepend-icon="lock" name="condirm_password"
                                              label="Повторите пароль" type="password" @keyup="onKeyup">
                                </v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="primary" @click="register">Зарегистрироватся</v-btn>
                            <v-spacer></v-spacer>
                            <v-btn flat color="orange" to="/login">Войти</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </v-app>
</template>

<script>
    import axios from 'axios';

    export default {
        middleware: 'authed',
        data: () => ({
            data: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            errors: {}
        }),

        methods: {
            async register () {
                this.errors = {};
                // Register the user.
                const {data} = await axios.post('/register', this.data).catch((errors)=>{
                    this.errors = errors.response.data.errors;
                });

                // Log in the user.
                const {data: {token}} = await axios.post('/login', this.data);

                // Save the token.
                this.$store.dispatch('auth/saveToken', {token});

                // Update the user.
                await this.$store.dispatch('auth/updateUser', {user: data});

                // Redirect home.
                this.$router.push('/');
            },
            onKeyup (event) {
                if (event.code === "Enter" || event.code === 'NumpadEnter') {
                    this.register();
                }
            },
            checkError (field) {
                return this.errors.hasOwnProperty(field) ? this.errors[field] : [];
            }
        }
    }
</script>
