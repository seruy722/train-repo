<template>

    <div class="main" data-component-name="Cargo">
        <v-container fluid>
            <v-toolbar color="white" dark>
                <v-toolbar-title class="text-xs-center title blue--text">Карго
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
                                <!--<v-icon dark right>block</v-icon>-->
                            </v-btn>
                            <v-btn
                                :disabled="loadOnBtn"
                                :loading="loadOnBtn"
                                class="white--text main_color-bg"
                                @click="save"
                            >
                                Сохранить
                                <!--<v-icon dark right>check_circle</v-icon>-->
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
            <v-data-table
                v-model="selected"
                :headers="headers"
                :items="desserts"
                item-key="name"
                select-all
                class="elevation-1"
            >
                <template slot="items" slot-scope="props">
                    <td>
                        <v-checkbox
                            v-model="props.selected"
                            primary
                            hide-details
                        ></v-checkbox>
                    </td>
                    <td>{{ props.item.name }}</td>
                    <td class="text-xs-right">{{ props.item.calories }}</td>
                    <td class="text-xs-right">{{ props.item.fat }}</td>
                    <td class="text-xs-right">{{ props.item.carbs }}</td>
                    <td class="text-xs-right">{{ props.item.protein }}</td>
                    <td class="text-xs-right">{{ props.item.iron }}</td>
                </template>
            </v-data-table>
        </v-container>
    </div>
</template>
<script>
    import {mapGetters, mapMutations} from 'vuex';
    import axios from 'axios';
    import Search from '~/components/Search';
    import {formatDate} from '~/utils';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        components: {
            Search
        },
        middleware: 'auth',
        mixins: [checkErrorMixin],
        async fetch ({store}) {
            const {data} = await axios.get('/blacklist');
            const list = data.data;
            const formatList = formatDate(list, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

            store.commit('blacklist/SET_LIST', formatList);
        },
        head () {
            return {title: `Карго`};
        },
        data () {
            return {
                dialog: false,
                search: '',
                imageName: null, // Имя загружаемого изображения
                previewImgSrc: null, // Код загружаемого изображения для предосмотра
                loadOnBtn: false, // Оверлей для кнопки
                selected: [],
                headers: [
                    {
                        text: 'Dessert (100g serving)',
                        align: 'left',
                        sortable: false,
                        value: 'name'
                    },
                    { text: 'Calories', value: 'calories' },
                    { text: 'Fat (g)', value: 'fat' },
                    { text: 'Carbs (g)', value: 'carbs' },
                    { text: 'Protein (g)', value: 'protein' },
                    { text: 'Iron (%)', value: 'iron' }
                ],
                desserts: [
                    {
                        value: false,
                        name: 'Frozen Yogurt',
                        calories: 159,
                        fat: 6.0,
                        carbs: 24,
                        protein: 4.0,
                        iron: '1%'
                    },
                    {
                        value: false,
                        name: 'Ice cream sandwich',
                        calories: 237,
                        fat: 9.0,
                        carbs: 37,
                        protein: 4.3,
                        iron: '1%'
                    },
                    {
                        value: false,
                        name: 'Eclair',
                        calories: 262,
                        fat: 16.0,
                        carbs: 23,
                        protein: 6.0,
                        iron: '7%'
                    },
                    {
                        value: false,
                        name: 'Cupcake',
                        calories: 305,
                        fat: 3.7,
                        carbs: 67,
                        protein: 4.3,
                        iron: '8%'
                    },
                    {
                        value: false,
                        name: 'Gingerbread',
                        calories: 356,
                        fat: 16.0,
                        carbs: 49,
                        protein: 3.9,
                        iron: '16%'
                    },
                    {
                        value: false,
                        name: 'Jelly bean',
                        calories: 375,
                        fat: 0.0,
                        carbs: 94,
                        protein: 0.0,
                        iron: '0%'
                    },
                    {
                        value: false,
                        name: 'Lollipop',
                        calories: 392,
                        fat: 0.2,
                        carbs: 98,
                        protein: 0,
                        iron: '2%'
                    },
                    {
                        value: false,
                        name: 'Honeycomb',
                        calories: 408,
                        fat: 3.2,
                        carbs: 87,
                        protein: 6.5,
                        iron: '45%'
                    },
                    {
                        value: false,
                        name: 'Donut',
                        calories: 452,
                        fat: 25.0,
                        carbs: 51,
                        protein: 4.9,
                        iron: '22%'
                    },
                    {
                        value: false,
                        name: 'KitKat',
                        calories: 518,
                        fat: 26.0,
                        carbs: 65,
                        protein: 7,
                        iron: '6%'
                    }
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
            }
        },
        computed: {
            dialogTitle () {
                return this.editedIndex === -1 ? 'Добавление клиента' : 'Редактирование клиента';
            },
            ...mapGetters({
                list: 'blacklist/getlist',
                currentUser: 'auth/user',
                defaultFoto: 'settings/defaultFoto',
                imageUrl: 'settings/imageUrl'
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
                    return this.previewImgSrc || `${this.imageUrl + imageSrc}` || this.defaultFoto;
                }
                return this.previewImgSrc || this.defaultFoto;
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
                    let {item} = response.data;
                    // Форматируем дату
                    item = formatDate(item, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

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
