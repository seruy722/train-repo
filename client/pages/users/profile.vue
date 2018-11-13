<template>
    <div class="main_profile_block" data-component-name="Profile">
        <v-container fluid>
            <v-layout row class="mb-3">
                <v-flex xs12 sm12 xs12 justify-center>
                    <v-toolbar color="white" dark evaluation-1>
                        <v-toolbar-title class="text-xs-center title blue--text">Профиль
                        </v-toolbar-title>
                    </v-toolbar>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex sm12 xs12 md12>
                    <v-layout row wrap class="text-xs-center">
                        <v-flex sm4 class="text-sm-center">
                            <v-card class="mb-3">
                                <v-card-text>
                                    <v-img
                                        :src="`${profileImage ? imageUrl + profileImage : defaultFoto}`"
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
                                        <v-tooltip
                                            top
                                            class="text-xs-center text-sm-center text-md-center"
                                        >
                                            <v-btn class="btn-bg-color"
                                                   @click="onSelectImage"
                                                   slot="activator"
                                            >
                                                <v-icon dark>add_photo_alternate</v-icon>
                                            </v-btn>
                                            <span>Заменить фото</span>
                                        </v-tooltip>
                                    </v-flex>
                                </v-layout>
                            </v-card>
                        </v-flex>
                        <v-flex sm8>
                            <v-card>
                                <v-card-title primary-title class="main_color-bg elevation-7">
                                    <span class="mr-2"><v-icon color="orange" size="35">settings</v-icon></span>
                                    <h3 class="title mb-0">Обновить профайл</h3>
                                </v-card-title>

                                <v-card-text>
                                    <v-layout row>
                                        <v-text-field
                                            label="Имя"
                                            v-model="data.name"
                                            :error-messages="checkError('name')"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-text-field
                                            label="Email"
                                            v-model="data.email"
                                            :error-messages="checkError('email')"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-flex xs12 class="text-xs-center">
                                            <v-btn @click="updateData()" class="btn-bg-color">
                                                <v-icon class="mr-2" dark>update</v-icon>
                                                Обновить
                                            </v-btn>
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                            </v-card>

                            <v-card class="mt-3">
                                <v-card-title primary-title class="main_color-bg elevation-7">
                                    <span class="mr-2"><v-icon color="orange" size="35">lock</v-icon></span>
                                    <h3 class="title mb-0">Обновить пароль</h3>
                                </v-card-title>
                                <v-card-text>
                                    <v-layout row>
                                        <v-text-field
                                            label="Новый пароль"
                                            v-model="data.password"
                                            :error-messages="checkError('password')"
                                            type="password"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-text-field
                                            label="Повторите пароль"
                                            v-model="data.password_confirmation"
                                            :error-messages="checkError('password')"
                                            type="password"
                                            class="pl-2 pr-2"
                                        ></v-text-field>
                                    </v-layout>

                                    <v-layout row>
                                        <v-flex xs12 class="text-xs-center">
                                            <v-btn @click="updatePassword()" class="btn-bg-color">
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
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        middleware: 'auth',
        mixins: [checkErrorMixin],
        data: () => ({
            data: {},
        }),
        head () {
            return {title: `Профиль ${this.user.name}`};
        },
        created () {
            this.init();
        },
        computed: {
            ...mapGetters({
                user: 'auth/user',
                profileImage: 'auth/userProfileImg',
                defaultFoto: 'settings/defaultFoto',
                imageUrl: 'settings/imageUrl'
            }),

        },
        methods: {
            init () {
                // Формирование данных профиля пользователя
                this.data = _.assign({}, {
                    name: this.user.name,
                    email: this.user.email,
                    password: null,
                    password_confirmation: null,
                    action: null
                });
            },
            async updateData () {
                this.changeErrors({});
                this.data.action = 'profile';

                await axios.patch('/settings/profile', this.data).then((response) => {
                    const {data} = response;
                    this.$store.dispatch('auth/updateUser', {user: data});
                    this.init();

                    this.$snotify.success('Данные успешно обновлены', {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true
                    });
                }).catch((errors) => {
                    this.changeErrors(errors.response.data.errors);
                });
            },
            async updatePassword () {
                this.changeErrors({});
                await axios.patch('/settings/password', this.data).then(() => {
                    this.$snotify.success('Пароль успешно изменен', {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true
                    });
                }).catch((errors) => {
                    this.changeErrors(errors.response.data.errors);
                });
            },
            onSelectImage () {
                this.$refs.profileimage.click();
            },
            async updatePhoto (event) {
                if (event.target.files && event.target.files.length) {
                    const file = event.target.files[0];
                    let dataFile = new FormData();

                    dataFile.append('profilephoto', file);

                    await axios.post('/settings/profile', dataFile).then((response) => {
                        const {data} = response;
                        this.$store.dispatch('auth/updateUser', {user: data});
                        this.$snotify.success('Фото успешно обновлено', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true
                        });
                    }).catch(() => {
                        this.$snotify.success('Неправильный формат изображения', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true
                        });
                    });
                    // Очистка input
                    this.$refs.profileimage.value = null;
                }
            },
        }
    };
</script>

<style scoped>
    .main_profile_block {
        width: 70%;
    }

    .profile-image-input {
        display: none;
    }
</style>
