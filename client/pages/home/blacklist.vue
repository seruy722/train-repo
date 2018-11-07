<template>

    <div class="main" data-component-name="BlackList">
        <v-toolbar flat color="white">
            <v-toolbar-title class="hidden-sm-and-down blue--text">Клиенты</v-toolbar-title>
            <v-divider
                class="mx-2 hidden-sm-and-down"
                inset
                vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Поиск"
                single-line
                hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" width="800px">
                <v-btn slot="activator" dark class="mb-2 main_color-bg">
                    <v-icon class="mr-2" dark>person_add</v-icon>
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
                                                            <input type="file" ref="profileimage" @change="updatePhoto"
                                                                   class="profile-image-input">
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
                                                            v-if="editedItem.foto && editedItem.foto !== 'nofoto.jpg'"
                                                            top
                                                            class="text-xs-center text-sm-center text-md-center"
                                                        >
                                                            <v-btn @click="deleteClientFoto" class="btn-bg-color"
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
                                            v-model="editedItem.fio"
                                            :error-messages="checkError('fio')"
                                            prepend-icon="person"
                                            autofocus
                                            placeholder="ФИО"
                                        ></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex
                                xs12 sm4 md4
                            >
                                <v-text-field
                                    v-model="editedItem.phone_1"
                                    :error-messages="checkError('phone_1')"
                                    type="tel"
                                    prepend-icon="phone"
                                    placeholder="(000) 000 - 0000"
                                    mask="phone"
                                ></v-text-field>
                            </v-flex>
                            <v-flex
                                xs12 sm4 md4
                            >
                                <v-text-field
                                    v-model="editedItem.phone_2"
                                    :error-messages="checkError('phone_2')"
                                    type="tel"
                                    prepend-icon="phone"
                                    placeholder="(000) 000 - 0000"
                                    mask="phone"
                                ></v-text-field>
                            </v-flex>
                            <v-flex
                                xs12 sm4 md4
                            >
                                <v-text-field
                                    v-model="editedItem.phone_3"
                                    :error-messages="checkError('phone_3')"
                                    type="tel"
                                    prepend-icon="phone"
                                    placeholder="(000) 000 - 0000"
                                    mask="phone"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm12 md12>
                                <v-text-field
                                    v-model="editedItem.notation"
                                    :error-messages="checkError('notation')"
                                    prepend-icon="notes"
                                    placeholder="Примечание"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="error" @click="dialog = false">
                            Отмена
                            <v-icon dark right>block</v-icon>
                        </v-btn>
                        <v-btn
                            :disabled="loadOnBtn"
                            :loading="loadOnBtn"
                            class="white--text main_color-bg"
                            @click="save"
                        >
                            Сохранить
                            <v-icon dark right>check_circle</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-toolbar>
        <v-data-table
            :headers="headers"
            :items="list"
            :search="search"
            disable-initial-sort
            class="elevation-1"
        >
            <v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>
            <template slot="items" slot-scope="props">
                <tr>
                    <td class="text-xs-center">{{ props.item.created_at }}</td>
                    <td class="text-xs-center">
                        <v-avatar
                            slot="activator"
                            size="36px"
                        >
                            <img
                                :src="`/images/${props.item.foto}`"
                                alt="Avatar"
                            >
                        </v-avatar>
                    </td>
                    <td class="text-xs-center">{{ props.item.fio }}</td>
                    <td class="text-xs-center">{{ props.item.phone_1 }}</td>
                    <td class="text-xs-center">{{ props.item.phone_2 }}</td>
                    <td class="text-xs-center">{{ props.item.phone_3 }}</td>
                    <td class="text-xs-center">{{ props.item.notation }}</td>
                    <td class="justify-center align-center layout px-0">
                        <v-tooltip top>
                            <v-icon
                                slot="activator"
                                class="mr-2"
                                color="teal"
                                @click="editItem(props.item)"
                            >
                                edit
                            </v-icon>
                            <span>Редактировать</span>
                        </v-tooltip>
                        <v-tooltip top>
                            <v-icon
                                slot="activator"
                                color="red"
                                @click="deleteItem(props.item)"
                            >
                                delete
                            </v-icon>
                            <span>Удалить</span>
                        </v-tooltip>
                    </td>
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
    </div>
</template>
<script>
    import {mapGetters, mapMutations} from 'vuex';
    import moment from 'moment';
    import axios from 'axios';

    export default {
        async fetch ({store}) {
            const {data} = await axios.get('/blacklist');
            const list = data.data;
            _.forEach(list, (item) => {
                item.created_at = moment(item.created_at, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY');
            });
            store.commit('blacklist/SET_LIST', list);
        },
        data: () => ({
            dialog: false,
            search: '',
            errors: {}, // Обьект ошибок возращаемый с сервера
            imageName: null, // Имя загружаемого изображения
            previewImgSrc: null, // Код загружаемого изображения для предосмотра
            loadOnBtn: false, // Оверлей для кнопки
            headers: [ // Заголовки таблицы
                {
                    text: 'Дата добавления',
                    align: 'center',
                    value: 'created_at'
                },
                {
                    text: 'Фото',
                    align: 'center',
                    sortable: false,
                    value: 'fio'
                },
                {
                    text: 'ФИО',
                    align: 'center',
                    value: 'fio'
                },
                {text: 'Телефон', align: 'center', value: 'phone_1'},
                {text: 'Телефон', align: 'center', value: 'phone_2'},
                {text: 'Телефон', align: 'center', value: 'phone_3'},
                {text: 'Примечание', align: 'center', value: 'notation'},
                {text: 'Actions', align: 'center', sortable: false}
            ],
            editedIndex: -1, // Для определения (добавление или сохранение)
            editedItem: {
                type: null,
                fio: null,
                foto: null,
                file: null,
                phone_1: null,
                phone_2: null,
                phone_3: null,
                notation: null,
            },
            defaultItem: {
                fio: null,
                foto: null,
                phone_1: null,
                phone_2: null,
                phone_3: null,
                notation: null,
            },
        }),

        computed: {
            dialogTitle () {
                return this.editedIndex === -1 ? 'Добавление клиента' : 'Редактирование клиента';
            },
            ...mapGetters({
                list: 'blacklist/getlist',
                currentUser: 'auth/user'
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
                if (this.editedItem.foto && this.editedItem.foto !== 'null') {
                    imageSrc = this.editedItem.foto;
                    return this.previewImgSrc || `/images/${imageSrc}` || '/nofoto.jpg';
                }
                return this.previewImgSrc || '/nofoto.jpg';
            }
        },

        watch: {
            dialog (val) {
                val || this.close();
            }
        },
        methods: {
            ...mapMutations({
                setList: 'blacklist/SET_LIST',
                addItemToStorage: 'blacklist/ADD_ITEM',
                updateItemInStorage: 'blacklist/UPDATE_ITEM',
                deleteItemFromStorage: 'blacklist/DELETE_ITEM',
                deleteItemFromServer: 'blacklist/DELETE_ITEM'
            }),
            editItem (item) {
                this.changeErrors({});
                this.editedIndex = _.indexOf(this.list, item);
                this.editedItem = _.assign({}, item);
                this.dialog = true;
            },
            changeErrors (value) {
                this.errors = value;
            },
            async deleteItem (item) {
                const index = this.list.indexOf(item);
                const answer = confirm('Удалить запись?');
                if (answer) {
                    await this.deleteItemFromServer(item.id).then((response) => {
                        const {status} = response.data;

                        if (status) {
                            this.deleteItemFromStorage(index);
                            this.saveLog(item, 'delete');
                            this.$snotify.success('Запись успешно удалена', {
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

            close () {
                this.dialog = false;
                this.loadOnBtn = false;
                setTimeout(() => {
                    this.editedItem = Object.assign({}, this.defaultItem);
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
            async save () {
                this.changeLoadBtn();
                this.changeErrors({});
                // Если удалено название изображения значит не отправляем изображение на сервер
                if (!this.imageName) {
                    this.editedItem.file = null;
                }
                // Если editedIndex > -1 значит это обновление записи
                if (this.editedIndex === -1) {
                    // СОХРАНЕНИЕ ДАННЫХ
                    this.editedItem.type = 'save';
                } else {
                    // ОБОВЛЕНИЕ ДАННЫХ
                    this.editedItem.type = 'update';
                }
                // Очищаем обьект от ложных данных
                const cleanedEditItem = _.pickBy(this.editedItem, _.identity);
                // Для отправки файлов помещаем свойства обьекта editedItem в FormData
                const data = new FormData();

                _.forEach(cleanedEditItem, (value, key) => {
                    if (!value) {
                        data.append(key, '');
                    } else {
                        data.append(key, value);
                    }
                });

                await this.saveItemToServer(data).then((response) => {
                    const {status} = response.data;
                    const {type} = response.data;
                    const {item} = response.data;

                    if (status) {
                        this.close();
                        this.saveLog(item, type);

                        if (type === 'update') {
                            const data = {item, index: this.editedIndex};
                            this.updateItemInStorage(data);
                            this.$snotify.success('Запись успешно обновлена', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        } else if (type === 'save') {
                            this.addItemToStorage(item);
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
            async saveItemToServer (item) {
                return await axios.post('blacklist/saveUpdate', item);
            },
            async deleteItemFromServer (id) {
                return await axios.post('blacklist/delete', {id});
            },
            checkError (field) {
                return this.errors.hasOwnProperty(field) ? this.errors[field] : [];
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
            async saveLog (item, type) {
                item.user_fio_id = `${this.currentUser.name}_${this.currentUser.id}`;
                item.action = type;

                await axios.post('blacklist/saveLog', item).catch((error) => {
                    this.saveLog({
                        action: 'ERROR',
                        user_fio_id: `${this.currentUser.name}_${this.currentUser.id}`,
                        notation: error
                    })
                });
            },
            async deleteClientFoto () {
                await axios.post('blacklist/deleteFoto', this.editedItem).then((response) => {
                    const {item} = response.data;
                    const data = {item, index: this.editedIndex};
                    this.updateItemInStorage(data);

                    this.$snotify.success('Изображение успешно удалено', {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true
                    });
                    this.editedItem.foto = item.foto;
                }).catch((error) => {
                    console.error(error);
                });
            }
        }
    }
</script>

<style lang="scss">
    .main {
        width: 95%;
    }

    .profile-image-input {
        display: none;
    }
</style>
