<template>
    <v-app id="inspire">
        <v-container fluid fill-height>
            <v-layout align-center justify-center>
                <v-flex xs12 sm8 md4>
                    <v-card class="elevation-12">
                        <v-toolbar dark color="primary">
                            <v-toolbar-title>Регистрация</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-card-text>
                            <v-form>
                                <v-text-field v-model="data.name" prepend-icon="person_pin" name="name" label="Имя"
                                              type="text"></v-text-field>
                                <v-text-field v-model="data.email" prepend-icon="person" name="email" label="E-mail"
                                              type="text"></v-text-field>
                                <v-text-field v-model="data.password" id="password" prepend-icon="lock" name="password"
                                              label="Пароль" type="password">
                                </v-text-field>
                                <v-text-field v-model="data.password_confirmation" id="confirm_password"
                                              prepend-icon="lock" name="condirm_password"
                                              label="Повторите пароль" type="password">
                                </v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="primary" @click="register">Зарегистрироватся</v-btn>
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
        data: () => ({
            data: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
        }),

        methods: {
            async register () {
                // Register the user.
                const {data} = await axios.post('/register', this.data);

                // Log in the user.
                const {data: {token}} = await axios.post('/login', this.data);

                // Save the token.
                this.$store.dispatch('auth/saveToken', {token});

                // Update the user.
                await this.$store.dispatch('auth/updateUser', {user: data});

                // Redirect home.
                this.$router.push('/');
            }
        }
    }
</script>
