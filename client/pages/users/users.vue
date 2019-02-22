<template>

    <div class="main" data-component-name="Users">
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
                    <v-card>
                        <v-card-title
                            class="grey lighten-4 py-4 title"
                        >
                            <span class="headline">{{ dialogTitle }}</span>
                        </v-card-title>
                        <v-container grid-list-sm class="pa-4">
                            <v-layout row wrap>
                                <v-flex xs12 align-center justify-space-between>
                                    <v-layout>
                                        <v-flex xs12 sm6 offset-sm3>
                                            <v-card>
                                                <v-container grid-list-sm fluid>
                                                    <v-layout row wrap justify-center>
                                                        <v-flex
                                                            xs12
                                                            sm12
                                                            md12
                                                            d-flex
                                                        >
                                                            <v-card flat tile class="d-flex">
                                                                <v-img
                                                                    :src="profileImage"
                                                                    :lazy-src="`https://picsum.photos/10/6?image=${1 * 5 + 10}`"
                                                                    aspect-ratio="1"
                                                                    ref="userImage"
                                                                    class="grey lighten-2"
                                                                >
                                                                    <v-layout
                                                                        slot="placeholder"
                                                                        fill-height
                                                                        align-center
                                                                        justify-center
                                                                        ma-0
                                                                    >
                                                                        <v-progress-circular
                                                                            indeterminate
                                                                            color="grey lighten-5"
                                                                        ></v-progress-circular>
                                                                    </v-layout>
                                                                </v-img>
                                                                <input type="file" ref="profileimage"
                                                                       @change="updatePhoto"
                                                                       class="display-none_input">
                                                            </v-card>

                                                        </v-flex>
                                                        <v-flex
                                                            xs12
                                                            sm12
                                                            md12
                                                            d-flex
                                                        >
                                                            <v-text-field
                                                                v-model="previewImageName"
                                                                :error-messages="checkError('file')"
                                                                readonly
                                                                clearable
                                                            >
                                                            </v-text-field>
                                                        </v-flex>
                                                        <v-flex
                                                            xs12
                                                            sm12
                                                            md12
                                                            d-flex
                                                        >
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
                                                            <v-tooltip
                                                                v-if="editedItem.set_photo_url && editedItem.set_photo_url !== 'nofoto.jpg'"
                                                                top
                                                                class="text-xs-center text-sm-center text-md-center"
                                                            >
                                                                <v-btn @click="deleteUserFoto" class="btn-bg-color"
                                                                       slot="activator">
                                                                    <v-icon>delete_forever</v-icon>
                                                                </v-btn>
                                                                <span>Удалить фото</span>
                                                            </v-tooltip>
                                                        </v-flex>
                                                    </v-layout>
                                                </v-container>
                                            </v-card>
                                        </v-flex>
                                    </v-layout>
                                    <v-layout align-center>
                                        <v-flex xs12 sm12 md12>
                                            <v-text-field
                                                v-model="editedItem.name"
                                                :error-messages="checkError('name')"
                                                prepend-icon="person"
                                                autofocus
                                                label="Имя"
                                            ></v-text-field>
                                        </v-flex>
                                    </v-layout>
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
                                        v-model="editedItem.password"
                                        :error-messages="checkError('password')"
                                        prepend-icon="lock"
                                        label="Пароль"
                                    ></v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-container>
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
                </v-dialog>
            </v-toolbar>
            <v-data-table
                :headers="headers"
                :items="userList"
                :search="search"
                disable-initial-sort
                class="elevation-1"
            >
                <v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>
                <template slot="items" slot-scope="props">
                    <tr>
                        <td class="justify-center align-center layout px-0">
                            <v-tooltip top>
                                <v-icon
                                    slot="activator"
                                    class="mr-2"
                                    color="teal"
                                    @click="editUser(props.item)"
                                >
                                    edit
                                </v-icon>
                                <span>Редактировать</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-icon
                                    slot="activator"
                                    color="red"
                                    @click="deleteUser(props.item)"
                                >
                                    delete
                                </v-icon>
                                <span>Удалить</span>
                            </v-tooltip>
                        </td>
                        <td class="text-xs-center">{{ props.item.name }}</td>
                        <td class="text-xs-center">
                            <v-avatar
                                slot="activator"
                                size="36px"
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
                        <td class="text-xs-center">{{ props.item.password }}</td>
                        <td class="text-xs-center">{{ props.item.created_at }}</td>
                    </tr>
                </template>
                <template slot="no-results">
                    <v-alert :value="true" color="error" icon="warning">
                        Поиск по "{{ search }}" не дал результатов.
                    </v-alert>
                </template>
                <template slot="no-data">
                    <v-alert :value="true" color="error" icon="warning">
                        Таблица пустая
                    </v-alert>
                </template>
            </v-data-table>
        </v-container>
    </div>
</template>
<script>
    import {mapGetters} from 'vuex';
    import axios from 'axios';
    import Search from '~/components/Search';
    import {formatDate} from '~/utils';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        components: {
            Search
        },
        middleware: 'admin',
        mixins: [checkErrorMixin],
        async asyncData () {
            const { data } = await axios.get('/users').catch((errors) => {
                console.error('Ошибка при запросе пользователей', errors);
            });
            const { users } = data;
            const formatList = formatDate(users, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

            return { userList: formatList };
        },
        head () {
            return {title: `Пользователи`};
        },
        data: () => ({
            dialog: false,
            search: '',
            imageName: null, // Имя загружаемого изображения
            previewImgSrc: null, // Код загружаемого изображения для предосмотра
            loadOnBtn: false, // Оверлей для кнопки
            headers: [ // Заголовки таблицы
                {text: 'Управление', align: 'center', sortable: false},
                {text: 'Имя', align: 'center', value: 'name'},
                {
                    text: 'Фото',
                    align: 'center',
                    sortable: false,
                    value: 'name'
                },
                {text: 'Email', align: 'center', value: 'email'},
                {text: 'Роль', align: 'center', value: 'role'},
                {text: 'Код', align: 'center', value: 'code'},
                {text: 'Пароль', align: 'center', value: 'name'},
                {
                    text: 'Дата добавления',
                    align: 'center',
                    value: 'created_at'
                }
            ],
            editedIndex: -1, // Для определения (добавление или сохранение)
            editedItem: {
                type: null,
                file: null,
                name: null,
                set_photo_url: null,
                email: null,
                role: null,
                code: null,
                password: null
            },
            defaultItem: {
                name: null,
                set_photo_url: null,
                email: null,
                role: null,
                code: null,
                password: null
            },
        }),

        computed: {
            dialogTitle () {
                return this.editedIndex === -1 ? 'Добавление пользователя' : 'Редактирование пользователя';
            },
            ...mapGetters({
                defaultFoto: 'settings/defaultFoto',
                imageUrl: 'settings/imageUrl',
                roles: 'auth/roles'
            }),
            // Предосмотр загружаемого изображения
            previewImageName: {
                get: function () {
                    return this.imageName
                },

                set: function () {
                    this.previewImgSrc = null;
                }
            },
            profileImage () {
                let imageSrc = null;
                if (this.editedItem.set_photo_url && this.editedItem.set_photo_url !== 'null') {
                    imageSrc = this.editedItem.set_photo_url;
                    return this.previewImgSrc || `${this.imageUrl + imageSrc}` || this.defaultFoto;
                }
                return this.previewImgSrc || this.defaultFoto;
            }
        },

        watch: {
            dialog (val) {
                val || this.closeDialog();
            }
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
                    const formatUserDate =  formatDate(user, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

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
                return await axios.post('users/delete', {id});
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
                await axios.post('users/deleteFoto', {id: this.editedItem.id}).then((response) => {
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
