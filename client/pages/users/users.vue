<template>
    <div
        class="main"
        data-component-name="Users"
    >
        <v-container fluid>
            <v-toolbar color="white" dark>
                <v-toolbar-title class="text-xs-center title blue--text">Пользователи
                </v-toolbar-title>
            </v-toolbar>
            <v-toolbar flat color="white" evaluation-1>

                <!--ПОИСК-->
                <search :value.sync="search"></search>

                <v-spacer></v-spacer>

                <v-dialog v-model="dialog" width="800px">
                    <v-btn slot="activator" dark class="mb-2 main_color-bg">
                        <!--<v-icon class="mr-2" dark>person_add</v-icon>-->
                        Добавить
                    </v-btn>
                    <template>
                        <v-card>
                            <v-card-title class="indigo white--text title">
                                {{ dialogTitle }}
                            </v-card-title>
                            <v-flex
                                d-flex
                                text-xs-center
                            >

                                <v-card
                                    class="pt-4 mx-auto"
                                    flat
                                    max-width="400"
                                >
                                    <v-card-text>
                                        <v-avatar
                                            size="100"
                                        >
                                            <v-img
                                                :src="profileImage"
                                                :lazy-src="`https://picsum.photos/10/6?image=${1 * 5 + 10}`"
                                                class="mb-4"
                                            ></v-img>
                                        </v-avatar>
                                        <div>
                                            <v-btn fab dark small color="cyan" @click="onSelectImage">
                                                <v-icon dark>edit</v-icon>
                                            </v-btn>

                                            <v-btn fab dark small color="red" @click="deleteUserFoto">
                                                <v-icon dark>delete</v-icon>
                                            </v-btn>
                                        </div>
                                        <div>
                                            <input type="file" ref="profileimage"
                                                   @change="updatePhoto"
                                                   class="display-none_input">
                                        </div>
                                    </v-card-text>
                                    <v-divider></v-divider>
                                    <v-layout
                                        tag="v-card-text"
                                        text-xs-left
                                        wrap
                                    >

                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.name"
                                                :error-messages="checkError('name')"
                                                prepend-icon="person"
                                                autofocus
                                                label="Имя"
                                            ></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.email"
                                                :error-messages="checkError('email')"
                                                prepend-icon="email"
                                                label="Email"
                                            ></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm12 md12>
                                            <v-select
                                                v-model="editedItem.role"
                                                :items="roles"
                                                :error-messages="checkError('role')"
                                                prepend-icon="view_list"
                                                label="Роль"
                                            ></v-select>
                                        </v-flex>
                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.code"
                                                :error-messages="checkError('code')"
                                                prepend-icon="how_to_reg"
                                                label="Код"
                                            ></v-text-field>
                                        </v-flex>

                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.sex"
                                                :error-messages="checkError('sex')"
                                                prepend-icon="how_to_reg"
                                                label="Пол"
                                            ></v-text-field>
                                        </v-flex>

                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.city"
                                                :error-messages="checkError('city')"
                                                prepend-icon="how_to_reg"
                                                label="Город"
                                            ></v-text-field>
                                        </v-flex>

                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.phone"
                                                :error-messages="checkError('phone')"
                                                prepend-icon="how_to_reg"
                                                label="Телефон"
                                            ></v-text-field>
                                        </v-flex>

                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.password"
                                                :error-messages="checkError('password')"
                                                prepend-icon="lock"
                                                label="Пароль"
                                            ></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="error" @click="dialog = false">
                                            Отмена
                                        </v-btn>
                                        <v-btn
                                            :disabled="loadOnBtn"
                                            :loading="loadOnBtn"
                                            class="white--text main_color-bg"
                                            @click="onSaveUser"
                                        >
                                            Сохранить
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-flex>
                        </v-card>
                    </template>
                    <!--<v-card>-->
                    <!--<v-card-title-->
                    <!--class="grey lighten-4 py-4 title"-->
                    <!--&gt;-->
                    <!--<span class="headline">{{ dialogTitle }}</span>-->
                    <!--</v-card-title>-->
                    <!--<v-container grid-list-sm class="pa-4">-->
                    <!--<v-layout row wrap>-->
                    <!--<v-flex xs12 align-center justify-space-between>-->
                    <!--<v-layout>-->
                    <!--<v-flex xs12 sm6 offset-sm3>-->
                    <!--<v-card>-->
                    <!--<v-container grid-list-sm fluid>-->
                    <!--<v-layout row wrap justify-center>-->
                    <!--<v-flex-->
                    <!--xs12-->
                    <!--sm12-->
                    <!--md12-->
                    <!--d-flex-->
                    <!--&gt;-->
                    <!--<v-card flat tile class="d-flex">-->
                    <!--<v-img-->
                    <!--:src="profileImage"-->
                    <!--:lazy-src="`https://picsum.photos/10/6?image=${1 * 5 + 10}`"-->
                    <!--aspect-ratio="1"-->
                    <!--ref="userImage"-->
                    <!--class="grey lighten-2"-->
                    <!--&gt;-->
                    <!--<v-layout-->
                    <!--slot="placeholder"-->
                    <!--fill-height-->
                    <!--align-center-->
                    <!--justify-center-->
                    <!--ma-0-->
                    <!--&gt;-->
                    <!--<v-progress-circular-->
                    <!--indeterminate-->
                    <!--color="grey lighten-5"-->
                    <!--&gt;</v-progress-circular>-->
                    <!--</v-layout>-->
                    <!--</v-img>-->
                    <!--<input type="file" ref="profileimage"-->
                    <!--@change="updatePhoto"-->
                    <!--class="display-none_input">-->
                    <!--</v-card>-->

                    <!--</v-flex>-->
                    <!--<v-flex-->
                    <!--xs12-->
                    <!--sm12-->
                    <!--md12-->
                    <!--d-flex-->
                    <!--&gt;-->
                    <!--<v-text-field-->
                    <!--v-model="previewImageName"-->
                    <!--:error-messages="checkError('file')"-->
                    <!--readonly-->
                    <!--clearable-->
                    <!--&gt;-->
                    <!--</v-text-field>-->
                    <!--</v-flex>-->
                    <!--<v-flex-->
                    <!--xs12-->
                    <!--sm12-->
                    <!--md12-->
                    <!--d-flex-->
                    <!--&gt;-->
                    <!--<v-tooltip-->
                    <!--top-->
                    <!--class="text-xs-center text-sm-center text-md-center"-->
                    <!--&gt;-->
                    <!--<v-btn class="btn-bg-color"-->
                    <!--@click="onSelectImage"-->
                    <!--slot="activator"-->
                    <!--&gt;-->
                    <!--<v-icon dark>add_photo_alternate</v-icon>-->
                    <!--</v-btn>-->
                    <!--<span>Заменить фото</span>-->
                    <!--</v-tooltip>-->
                    <!--<v-tooltip-->
                    <!--v-if="editedItem.set_photo_url && editedItem.set_photo_url !== 'nofoto.jpg'"-->
                    <!--top-->
                    <!--class="text-xs-center text-sm-center text-md-center"-->
                    <!--&gt;-->
                    <!--<v-btn @click="deleteUserFoto" class="btn-bg-color"-->
                    <!--slot="activator">-->
                    <!--<v-icon>delete_forever</v-icon>-->
                    <!--</v-btn>-->
                    <!--<span>Удалить фото</span>-->
                    <!--</v-tooltip>-->
                    <!--</v-flex>-->
                    <!--</v-layout>-->
                    <!--</v-container>-->
                    <!--</v-card>-->
                    <!--</v-flex>-->
                    <!--</v-layout>-->
                    <!--<v-layout align-center>-->
                    <!--<v-flex xs12 sm12 md12>-->
                    <!--<v-text-field-->
                    <!--v-model="editedItem.name"-->
                    <!--:error-messages="checkError('name')"-->
                    <!--prepend-icon="person"-->
                    <!--autofocus-->
                    <!--label="Имя"-->
                    <!--&gt;</v-text-field>-->
                    <!--</v-flex>-->
                    <!--</v-layout>-->
                    <!--</v-flex>-->
                    <!--<v-flex xs12 sm12 md12>-->
                    <!--<v-text-field-->
                    <!--v-model="editedItem.email"-->
                    <!--:error-messages="checkError('email')"-->
                    <!--prepend-icon="email"-->
                    <!--label="Email"-->
                    <!--&gt;</v-text-field>-->
                    <!--</v-flex>-->
                    <!--<v-flex xs12 sm12 md12>-->
                    <!--<v-select-->
                    <!--v-model="editedItem.role"-->
                    <!--:items="roles"-->
                    <!--:error-messages="checkError('role')"-->
                    <!--prepend-icon="view_list"-->
                    <!--label="Роль"-->
                    <!--&gt;</v-select>-->
                    <!--</v-flex>-->
                    <!--<v-flex xs12 sm12 md12>-->
                    <!--<v-text-field-->
                    <!--v-model="editedItem.code"-->
                    <!--:error-messages="checkError('code')"-->
                    <!--prepend-icon="how_to_reg"-->
                    <!--label="Код"-->
                    <!--&gt;</v-text-field>-->
                    <!--</v-flex>-->

                    <!--<v-flex xs12 sm12 md12>-->
                    <!--<v-text-field-->
                    <!--v-model="editedItem.password"-->
                    <!--:error-messages="checkError('password')"-->
                    <!--prepend-icon="lock"-->
                    <!--label="Пароль"-->
                    <!--&gt;</v-text-field>-->
                    <!--</v-flex>-->
                    <!--</v-layout>-->
                    <!--</v-container>-->
                    <!--<v-card-actions>-->
                    <!--<v-spacer></v-spacer>-->
                    <!--<v-btn color="error" @click="dialog = false">-->
                    <!--Отмена-->
                    <!--</v-btn>-->
                    <!--<v-btn-->
                    <!--:disabled="loadOnBtn"-->
                    <!--:loading="loadOnBtn"-->
                    <!--class="white&#45;&#45;text main_color-bg"-->
                    <!--@click="onSaveUser"-->
                    <!--&gt;-->
                    <!--Сохранить-->
                    <!--</v-btn>-->
                    <!--</v-card-actions>-->
                    <!--</v-card>-->
                </v-dialog>
            </v-toolbar>
            <v-data-table
                :headers="$tableHeaders"
                :items="userList"
                :search="search"
                disable-initial-sort
                class="elevation-1"
            >
                <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
                <template v-slot:items="props">
                    <tr @dblclick="editUser(props.item)">
                        <!--<td class="justify-center align-center layout px-0">-->
                        <!--<v-tooltip top>-->
                        <!--<v-icon-->
                        <!--slot="activator"-->
                        <!--class="mr-2"-->
                        <!--color="teal"-->
                        <!--@click="editUser(props.item)"-->
                        <!--&gt;-->
                        <!--edit-->
                        <!--</v-icon>-->
                        <!--<span>Редактировать</span>-->
                        <!--</v-tooltip>-->
                        <!--<v-tooltip top>-->
                        <!--<v-icon-->
                        <!--slot="activator"-->
                        <!--color="red"-->
                        <!--@click="deleteUser(props.item)"-->
                        <!--&gt;-->
                        <!--delete-->
                        <!--</v-icon>-->
                        <!--<span>Удалить</span>-->
                        <!--</v-tooltip>-->
                        <!--</td>-->
                        <td class="text-xs-center">{{ props.item.name | upperFirst }}</td>
                        <td class="text-xs-center">
                            <v-avatar
                                size="36"
                            >
                                <img
                                    :src="`${props.item.set_photo_url ? imageUrl + props.item.set_photo_url : defaultFoto}`"
                                    alt="Avatar"
                                >
                            </v-avatar>
                        </td>
                        <td class="text-xs-center">{{ props.item.email }}</td>
                        <td class="text-xs-center">{{ props.item.role }}</td>
                        <td class="text-xs-center">{{ props.item.code }}</td>
                        <td class="text-xs-center">{{ props.item.sex }}</td>
                        <td class="text-xs-center">{{ props.item.city }}</td>
                        <td class="text-xs-center">{{ props.item.phone }}</td>
                        <!--<td class="text-xs-center">{{ props.item.password }}</td>-->
                        <td class="text-xs-center">{{ props.item.created_at | formatDate }}</td>
                    </tr>
                </template>
                <template v-slot:no-results>
                    <v-alert :value="true" color="error" icon="warning">
                        Поиск по "{{ search }}" не дал результатов.
                    </v-alert>
                </template>
                <template v-slot:no-data>
                    <v-alert :value="true" color="error" icon="warning">
                        Таблица пустая
                    </v-alert>
                </template>
            </v-data-table>
        </v-container>
    </div>
</template>
<script>
    import { mapGetters } from 'vuex';
    import axios from 'axios';
    import { formatDate } from '~/utils';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        name: 'Users',
        components: {
            Search: () => import('~/components/Search'),
        },
        middleware: 'admin',
        mixins: [checkErrorMixin],

        head () {
            return { title: 'Пользователи' };
        },
        data () {
            this.$tableHeaders = [
                { text: 'Имя', align: 'center', value: 'name' },
                {
                    text: 'Фото',
                    align: 'center',
                    sortable: false,
                    value: 'name',
                },
                { text: 'Email', align: 'center', value: 'email' },
                { text: 'Роль', align: 'center', value: 'role' },
                { text: 'Код', align: 'center', value: 'code' },
                { text: 'Пол', align: 'center', value: 'sex' },
                { text: 'Город', align: 'center', value: 'city' },
                { text: 'Телефон', align: 'center', value: 'phone' },
                {
                    text: 'Дата добавления',
                    align: 'center',
                    value: 'created_at',
                },
            ];

            this.$defaultItem = {
                type: null,
                file: null,
                name: null,
                set_photo_url: null,
                email: null,
                role: null,
                code: null,
                sex: null,
                city: null,
                phone: null,
                password: null,
            };

            return {
                dialog: false,
                search: '',
                imageName: null, // Имя загружаемого изображения
                previewImgSrc: null, // Код загружаемого изображения для предосмотра
                loadOnBtn: false, // Оверлей для кнопки
                editedIndex: -1, // Для определения (добавление или сохранение)
                editedItem: {
                    type: null,
                    file: null,
                    name: null,
                    set_photo_url: null,
                    email: null,
                    role: null,
                    code: null,
                    sex: null,
                    city: null,
                    phone: null,
                    password: null,
                },
            };
        },

        computed: {
            dialogTitle () {
                return this.editedIndex === -1 ? 'Добавление пользователя' : 'Редактирование пользователя';
            },
            ...mapGetters({
                defaultFoto: 'settings/defaultFoto',
                imageUrl: 'settings/imageUrl',
                roles: 'auth/roles',
            }),
            // Предосмотр загружаемого изображения
            previewImageName: {
                get: function () {
                    return this.imageName;
                },

                set: function () {
                    this.previewImgSrc = null;
                },
            },
            profileImage () {
                let imageSrc = null;
                if (this.editedItem.set_photo_url && this.editedItem.set_photo_url !== 'null') {
                    imageSrc = this.editedItem.set_photo_url;
                    return this.previewImgSrc || `${this.imageUrl + imageSrc}` || this.defaultFoto;
                }
                return this.previewImgSrc || this.defaultFoto;
            },
        },

        watch: {
            dialog (val) {
                val || this.closeDialog();
            },
        },
        async asyncData () {
            const { data } = await axios.get('/users').catch((errors) => {
                console.error('Ошибка при запросе пользователей', errors);
            });
            const { users = [] } = data;
            // const formatList = formatDate(users, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

            return { userList: users };
        },
        created () {
            console.log('LIST_USERS', this.userList);
        },
        methods: {
            editUser (item) {
                this.changeErrors({});
                this.editedIndex = _.indexOf(this.userList, item);
                this.editedItem = _.assign({}, item);
                this.dialog = true;
            },
            async deleteUser (item) {
                const index = _.indexOf(this.userList, item);
                const answer = confirm('Удалить пользователя?');
                if (answer) {
                    await this.deleteUserFromServer(item.id).then((response) => {
                        const { status } = response.data;

                        if (status) {
                            this.userList.splice(index, 1);
                            this.$snotify.success('Пользователь успешно удален!', {
                                timeout: 2000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        }
                    }).catch(() => {
                        this.$snotify.warning('Произошла ошибка при удалении записи! Перезагрузите пожалуйста страницу', {
                            timeout: 2000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true
                        });
                    });
                }
            },

            closeDialog () {
                this.dialog = false;
                this.loadOnBtn = false;
                setTimeout(() => {
                    this.editedItem = _.assign({}, this.defaultItem);
                    this.editedIndex = -1;
                }, 300);
                // ИСПРАВИТЬ
                this.imageName = null;
                this.previewImgSrc = null;
                this.changeErrors({});
            },
            changeLoadBtn () {
                this.loadOnBtn = !this.loadOnBtn;
            },
            async onSaveUser () {
                this.changeLoadBtn();
                this.changeErrors({});
                // Если удалено название изображения значит не отправляем изображение на сервер
                if (!this.imageName) {
                    this.editedItem.file = null;
                }
                if (this.editedIndex === -1) {
                    // СОХРАНЕНИЕ ДАННЫХ
                    this.editedItem.type = 'save';
                } else {
                    // ОБОВЛЕНИЕ ДАННЫХ
                    this.editedItem.type = 'update';
                }
                // Очищаем обьект от ложных данных
                const cleanedSendObject = _.pickBy(this.editedItem, _.identity);
                // Для отправки файлов помещаем свойства обьекта editedItem в FormData
                const sendData = new FormData();

                _.forEach(cleanedSendObject, (value, key) => {
                    if (!value) {
                        sendData.append(key, '');
                    } else {
                        sendData.append(key, value);
                    }
                });

                await this.saveUserToServer(sendData).then((response) => {
                    const { status, type, user } = response.data;
                    const formatUserDate = formatDate(user, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

                    if (status) {
                        this.closeDialog();
                        if (type === 'update') {
                            const userIndex = this.editedIndex;
                            this.userList.splice(userIndex, 1, formatUserDate);

                            this.$snotify.success('Запись успешно обновлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        } else if (type === 'save') {
                            this.userList.unshift(formatUserDate);
                            this.$snotify.success('Запись успешно добавлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        }
                    }
                }).catch((errors) => {
                    this.changeLoadBtn();
                    this.changeErrors(errors.response.data.errors);
                });
            },
            async saveUserToServer (data) {
                return await axios.post('users/saveUpdate', data);
            },
            async deleteUserFromServer (id) {
                return await axios.post('users/delete', { id });
            },
            onSelectImage () {
                this.$refs.profileimage.click();
            },
            updatePhoto (event) {
                if (event.target.files && event.target.files.length) {
                    const file = event.target.files[0];
                    this.imageName = event.target.files[0].name;

                    // Получаем изображение в виде строки для предосмотра
                    this.previewImgSrc = URL.createObjectURL(file);
                    this.editedItem.file = file;

                }
            },
            async deleteUserFoto () {
                await axios.post('users/deleteFoto', { id: this.editedItem.id }).then((response) => {
                    const { user } = response.data;
                    const formatUserDate = formatDate(user, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');
                    this.userList.splice(this.editedIndex, 1, formatUserDate);
                    this.$snotify.success('Изображение успешно удалено!', {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true
                    });
                    this.editedItem.set_photo_url = user.set_photo_url;
                }).catch((error) => {
                    console.error(error);
                });
            }
        }
    }
</script>
