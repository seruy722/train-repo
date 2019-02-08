<template>
    <div data-vue-component-name="Login">
        <v-app id="inspire">
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md4>
                        <v-card class="elevation-12">
                            <v-toolbar dark color="primary">
                                <v-toolbar-title>Вход</v-toolbar-title>
                                <v-spacer></v-spacer>
                                <v-img
                                    :src="logoUrl"
                                    :lazy-src="`https://picsum.photos/10/6?image=${2 * 5 + 10}`"
                                    aspect-ratio="1.7"
                                    contain
                                    height="40px"
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
                                <v-form v-model="isUserLoginFormValid">
                                    <v-text-field
                                        v-model.trim="userLoginData.email"
                                        prepend-icon="person"
                                        label="E-mail"
                                        type="text"
                                        maxlength="255"
                                        required
                                        :rules="[rules.email]"
                                        :error-messages="checkError('email')"
                                        @keyup="onKeyUp"
                                    >
                                        >
                                    </v-text-field>

                                    <v-text-field
                                        id="password"
                                        v-model.trim="userLoginData.password"
                                        prepend-icon="lock"
                                        label="Пароль"
                                        type="password"
                                        counter
                                        maxlength="255"
                                        required
                                        :error-messages="checkError('password')"
                                        @keyup="onKeyUp"
                                    >
                                    </v-text-field>
                                </v-form>
                            </v-card-text>
                            <v-card-actions align-center justify-space-around>
                                <v-btn
                                    :disabled="!isUserLoginFormValid"
                                    :loading="loadOnBtn"
                                    color="primary"
                                    @click="login"
                                >
                                    Войти
                                </v-btn>
                                <v-spacer></v-spacer>
                                <v-btn flat color="orange" to="/register">Регистрация</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-app>
    </div>
</template>

<script>
    import axios from 'axios';
    import checkErrorMixin from '~/mixins/checkError';
    import { mapGetters } from 'vuex';

    export default {
        name: 'Login',
        middleware: 'authed',
        mixins: [checkErrorMixin],
        data: () => ({
            userLoginData: {
                email: null,
                password: null,
            },
            loadOnBtn: false,
            isUserLoginFormValid: false,
            rules: {
                email: (value) => {
                    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return pattern.test(value) || 'Неправильный e-mail.';
                },
            },
        }),
        computed: {
            ...mapGetters({
                logoUrl: 'settings/logoUrl',
            }),
        },
        methods: {
            async login () {
                this.startLoadBtn(true);
                this.isUserLoginFormValid = false;
                this.changeErrors({});

                await axios.post('/login', this.userLoginData).then((response) => {
                    // Save the token.
                    this.$store.dispatch('auth/saveToken', {
                        token: response.data.token,
                        remember: this.remember,
                    });
                    // Fetch the user.
                    this.$store.dispatch('auth/fetchUser');

                    // Redirect home.
                    this.$router.push('/');
                }).catch((errors) => {
                    this.startLoadBtn(false);
                    this.changeErrors(errors.response.data.errors);
                });
            },
            onKeyUp (event) {
                if (event.code === 'Enter' || event.code === 'NumpadEnter') {
                    if (this.isUserLoginFormValid) {
                        this.login();
                    }
                } else {
                    this.changeErrors({});
                }
            },
            startLoadBtn (bool) {
                this.loadOnBtn = bool;
            },
        },
    };
</script>
