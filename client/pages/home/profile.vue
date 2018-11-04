<template>
    <div class="main_profile_block" data-component-name="Profile">
        <v-container fluid>
            <v-layout row class="mb-3" justify-center>
                <v-flex xs12 sm12 xs12>
                    <v-card>
                        <v-toolbar color="white" dark>
                            <v-toolbar-title class="text-xs-center headline blue--text" >Профиль пользователя</v-toolbar-title>
                        </v-toolbar>
                    </v-card>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex sm12 xs12 md12>
                    <v-layout row wrap class="text-xs-center">
                        <v-flex sm4 class="pr-3 text-sm-center">
                            <v-card class="pt-2 mr-3 mb-3">
                                <v-card-text>
                                    <v-img
                                        :src="profileImage"
                                        aspect-ratio="1"
                                        class="grey lighten-2"
                                    >
                                        <v-layout
                                            slot="placeholder"
                                            fill-height
                                            align-center
                                            justify-center
                                            ma-0
                                        >
                                            <v-progress-circular indeterminate
                                                                 color="grey lighten-5"></v-progress-circular>
                                        </v-layout>
                                    </v-img>
                                    <input type="file" ref="profileimage" @change="updatePhoto"
                                           class="profile-image-input">
                                </v-card-text>
                                <v-layout row>
                                    <v-flex xs12 class="text-xs-center">
                                        <v-btn color="orange" @click="onSelectImage">
                                            <v-icon class="mr-2" dark>add_photo_alternate</v-icon>
                                            Обновить фото
                                        </v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-card>
                        </v-flex>
                        <v-flex sm8>
                            <v-card>
                                <v-card-title primary-title class="card_title_bg elevation-7">
                                    <span class="mr-2"><v-icon color="orange" size="35">settings</v-icon></span>
                                    <h3 class="title mb-0">Обновить профайл</h3>
                                </v-card-title>

                                <v-card-text>
                                    <v-layout row>
                                        <v-text-field
                                            label="Имя"
                                            v-model="form.name"
                                            :error-messages="checkError('name')"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-text-field
                                            label="Email"
                                            v-model="form.email"
                                            :error-messages="checkError('email')"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-flex xs12 class="text-xs-center">
                                            <v-btn @click="updateData()" color="orange">
                                                <v-icon class="mr-2" dark>update</v-icon>
                                                Обновить
                                            </v-btn>
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                            </v-card>

                            <v-card class="mt-3">
                                <v-card-title primary-title class="card_title_bg elevation-7">
                                    <span class="mr-2"><v-icon color="orange" size="35">lock</v-icon></span>
                                    <h3 class="title mb-0">Обновить пароль</h3>
                                </v-card-title>
                                <v-card-text>
                                    <v-layout row>
                                        <v-text-field
                                            label="Новый пароль"
                                            v-model="form.password"
                                            :error-messages="checkError('password')"
                                            type="password"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-text-field
                                            label="Повторите пароль"
                                            v-model="form.password_confirmation"
                                            :error-messages="checkError('password')"
                                            type="password"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-flex xs12 class="text-xs-center">
                                            <v-btn @click="updatePassword()" color="orange">
                                                <v-icon class="mr-2" dark>update</v-icon>
                                                Обновить
                                            </v-btn>
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import axios from 'axios';

    export default {
        middleware: 'auth',
        data () {
            return {
                form: {},
                errors: {},
                properties: {
                    password: null,
                    password_confirmation: null,
                    action: null,
                },
            };
        },
        head () {
            return {title: `Профиль ${this.user.name}`};
        },
        created () {
            this.form = _.clone(this.user);
            this.addProperties(this.properties);
        },
        computed: {
            ...mapGetters({
                user: 'auth/user',
                profileImage: 'auth/userProfileImg',
            }),
        },
        methods: {
            // Дополнительные  свойства
            addProperties (props) {
                _.forEach(props, (item, index) => {
                    this.form[index] = item;
                });
            },
            async updateData () {
                this.form.action = 'profile';

                await axios.patch('/settings/profile', this.form).then((response)=>{
                    const { data }= response;
                    this.$store.dispatch('auth/updateUser', {user: data});

                    this.$notify({
                        group: 'profile',
                        type: 'success',
                        title: 'ОБНОВЛЕНИЕ',
                        text: 'Данные успешно обновлены!'
                    });
                }).catch((errors)=>{
                    this.errors = errors.response.data.errors;
                });



            },
            async updatePassword () {
                this.errors = {};
                await axios.patch('/settings/password', this.form).then(() => {
                    this.$notify({
                        group: 'profile',
                        type: 'success',
                        title: 'ОБНОВЛЕНИЕ',
                        text: 'Пароль успешно изменен!'
                    });
                }).catch((error) => {
                    this.errors = error.response.data.errors;
                });
            },
            checkError (field) {
                return this.errors.hasOwnProperty(field) ? this.errors[field] : [];
            },
            onSelectImage () {
                this.$refs.profileimage.click();
            },
            async updatePhoto (e) {
                if (e.target.files && e.target.files.length) {
                    const file = e.target.files[0];
                    let dataFile = new FormData();

                    dataFile.append('profilephoto', file);

                    await axios.post('/settings/profile', dataFile).then((response)=>{
                        const { data } = response;
                        this.$store.dispatch('auth/updateUser', {user: data});
                        this.$notify({
                            group: 'profile',
                            type: 'success',
                            title: 'ОБНОВЛЕНИЕ',
                            text: 'Фото успешно обновлено!'
                        });
                    }).catch(() => {
                        this.$notify({
                            group: 'profile',
                            type: 'error',
                            title: 'ОБНОВЛЕНИЕ',
                            text: 'Неправильный формат изображения'
                        });
                    });
                    // Очистка input
                    this.$refs.profileimage.value = null;
                }
            },
        }
    };
</script>

<style>
    .main_profile_block {
        width: 70%;
    }

    .card_title_bg {
        color: #fff;
        background-color: #1565C0;
    }

    .profile-image-input {
        display: none;
    }
</style>
