<template>
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
                            <v-form>
                                <v-text-field v-model="data.email" prepend-icon="person" name="email" label="E-mail"
                                              type="text" :rules="emailRules"
                                              :error-messages="checkError('email')"></v-text-field>
                                <v-text-field v-model="data.password" id="password" prepend-icon="lock" name="password"
                                              label="Пароль" type="password" :error-messages="checkError('password')"
                                              @keyup="onKeyup">
                                </v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions align-center justify-space-around>
                            <v-btn
                                :disabled="loadOnBtn"
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
</template>

<script>
    import axios from 'axios';
    import checkErrorMixin from '~/mixins/checkError';
    import { mapGetters } from 'vuex';

    export default {
        middleware: 'authed',
        mixins: [checkErrorMixin],
        data: () => ({
            data: {
                email: null,
                password: null,
            },
            loadOnBtn: false,
            emailRules: [],
        }),
        computed: {
            ...mapGetters({
                logoUrl: 'settings/logoUrl',
            }),
        },
        watch: {
            'data.email': function (mail) {
                if (mail !== '') {
                    this.emailRules = [v => (v.match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) || 'Неправильный Email адрес'];
                } else if (mail === '') {
                    this.emailRules = [];
                }
            },
        },
        methods: {
            async login () {
                this.loadOnBtn = true;
                this.changeErrors({});

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
                }).catch((errors) => {
                    this.loadOnBtn = false;
                    this.changeErrors(errors.response.data.errors);
                });
            },
            onKeyup (event) {
                if (event.code === 'Enter' || event.code === 'NumpadEnter') {
                    this.login();
                }
            },
        },
    };
</script>
