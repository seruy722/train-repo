<template>

    <div class="main" data-component-name="Emails">
        <v-container fluid>
            <v-toolbar color="white" dark>
                <v-toolbar-title class="text-xs-center title blue--text">Emails для входа на сайт
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
                                <v-flex xs12 sm12 md12>
                                    <v-text-field
                                        v-model="editedItem.email"
                                        :error-messages="checkError('email')"
                                        prepend-icon="email"
                                        label="Email"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm12 md12>
                                    <v-text-field
                                        v-model="editedItem.notation"
                                        :error-messages="checkError('notation')"
                                        prepend-icon="notes"
                                        label="Примечание"
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
                :items="emailsList"
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
                                    @click="editEmail(props.item)"
                                >
                                    edit
                                </v-icon>
                                <span>Редактировать</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-icon
                                    slot="activator"
                                    color="red"
                                    @click="deleteEmail(props.item)"
                                >
                                    delete
                                </v-icon>
                                <span>Удалить</span>
                            </v-tooltip>
                        </td>
                        <td class="text-xs-center">{{ props.item.email }}</td>
                        <td class="text-xs-center">{{ props.item.notation }}</td>
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
        async asyncData () {
            const { data } = await axios.get('/emails').catch((errors) => {
                console.error('Ошибка при запросе имейлов', errors);
            });
            const { emails } = data;
            const formatList = formatDate(emails, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

            return { emailsList: formatList };
        },
        head () {
            return {title: `Emails`};
        },
        components: {
            Search
        },
        middleware: 'admin',
        mixins: [checkErrorMixin],
        data: () => ({
            dialog: false,
            search: '',
            loadOnBtn: false, // Оверлей для кнопки
            headers: [ // Заголовки таблицы
                {text: 'Управление', align: 'center', sortable: false},
                {text: 'Email', align: 'center', value: 'email'},
                {text: 'Примечание', align: 'center', value: 'notation'},
                {
                    text: 'Дата добавления',
                    align: 'center',
                    value: 'created_at'
                }
            ],
            editedIndex: -1, // Для определения (добавление или сохранение)
            editedItem: {
                type: null,
                email: null,
                notation: null
            },
            defaultItem: {
                email: null,
                notation: null
            },
        }),

        computed: {
            dialogTitle () {
                return this.editedIndex === -1 ? 'Добавление E-mail' : 'Редактирование E-mail';
            }
        },
        watch: {
            dialog (val) {
                val || this.closeDialog();
            }
        },
        methods: {
            editEmail (item) {
                this.changeErrors({});
                this.editedIndex = _.indexOf(this.emailsList, item);
                this.editedItem = _.assign({}, item);
                this.dialog = true;
            },
            async deleteEmail (item) {
                const index = _.indexOf(this.emailsList, item);
                const answer = confirm('Удалить email?');
                if (answer) {
                    await this.deleteEmailFromServer(item.id).then((response) => {
                        const { status } = response.data;

                        if (status) {
                            this.emailsList.splice(index, 1);
                            this.$snotify.success('Запись успешно удалена!', {
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

                if (this.editedIndex === -1) {
                    // СОХРАНЕНИЕ ДАННЫХ
                    this.editedItem.type = 'save';
                } else {
                    // ОБОВЛЕНИЕ ДАННЫХ
                    this.editedItem.type = 'update';
                }
                // Очищаем обьект от ложных данных
                const cleanedSendObject = _.pickBy(this.editedItem, _.identity);

                const sendData = new FormData();
                // Для отправки файлов помещаем свойства обьекта editedItem в FormData
                _.forEach(cleanedSendObject, (value, key) => {
                    sendData.append(key, value);
                });

                await this.saveEmailToServer(sendData).then((response) => {
                    const { status, type, email } = response.data;
                    const formatEmailDate =  formatDate(email, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

                    if (status) {
                        this.closeDialog();
                        if (type === 'update') {
                            const emailIndex = this.editedIndex;
                            this.emailsList.splice(emailIndex, 1, formatUserDate);

                            this.$snotify.success('Запись успешно обновлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        } else if (type === 'save') {
                            this.emailsList.unshift(formatEmailDate);
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
            async saveEmailToServer (data) {
                return await axios.post('email/saveUpdate', data);
            },
            async deleteEmailFromServer (id) {
                return await axios.post('email/delete', {id});
            }
        }
    }
</script>
