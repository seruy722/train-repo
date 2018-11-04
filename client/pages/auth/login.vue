<template>
    <v-app id="inspire">
        <v-container fluid fill-height>
            <v-layout align-center justify-center>
                <v-flex xs12 sm8 md4>
                    <v-card class="elevation-12">
                        <v-toolbar dark color="primary">
                            <v-toolbar-title>Вход</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-card-text>
                            <v-form>
                                <v-text-field v-model="data.email" prepend-icon="person" name="email" label="E-mail"
                                              type="text" :error-messages="checkError('email')"></v-text-field>
                                <v-text-field v-model="data.password" id="password" prepend-icon="lock" name="password"
                                              label="Пароль" type="password" :error-messages="checkError('password')"
                                              @keyup="onKeyup">
                                </v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions align-center justify-space-around>
                            <v-btn color="primary" @click="login">Войти</v-btn>
                            <v-spacer></v-spacer>
                            <v-btn flat color="orange" to="/register">Регистрация</v-btn>
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
                email: null,
                password: null
            },
            errors: {},
        }),
        methods: {
            async login () {
                this.errors = {};

                await axios.post('/login', this.data).then((response) => {
                    // Save the token.
                    this.$store.dispatch('auth/saveToken', {
                        token: response.data.token,
                        remember: this.remember,
                    });
                    // Fetch the user.
                    this.$store.dispatch('auth/fetchUser');

                    // Redirect home.
                    this.$router.push('/');
                }).catch(error => {
                    this.errors = error.response.data.errors;
                });
            },
            checkError (field) {
                return this.errors.hasOwnProperty(field) ? this.errors[field] : [];
            },
            onKeyup (e) {
                if (e.code === "Enter" || e.code === 'NumpadEnter') {
                    this.login();
                }
            }
        }
    }
</script>

