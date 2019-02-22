<template>
    <div
        class="main"
        data-component-name="Faxes"
    >
        <v-container fluid>

            <v-toolbar>
                <v-toolbar-title class="text-xs-center title blue--text">Факсы</v-toolbar-title>
                <v-spacer></v-spacer>
                <Search :value.sync="search"/>
            </v-toolbar>

            <v-toolbar>
                <v-toolbar-items v-show="selected.length">
                    <v-flex xs12>
                        <v-btn outline fab small color="indigo">
                            <v-icon dark>edit</v-icon>
                        </v-btn>
                    </v-flex>

                    <v-flex xs12>
                        <v-btn outline fab small color="error">
                            <v-icon dark>delete</v-icon>
                        </v-btn>
                    </v-flex>
                </v-toolbar-items>

                <v-spacer></v-spacer>
                <!--Диалог загрузки факса-->
                <template>
                    <div class="text-xs-center">
                        <DefaultButton
                            @clickButton="openCloseDialog(true)"
                            :defaultButtonSettings="$_defaultButtonAddFaxSettings"
                        />

                        <v-dialog
                            v-model="dialog"
                            persistent
                            max-width="500"
                        >
                            <v-card>
                                <v-card-title
                                    class="headline grey lighten-2"
                                    primary-title
                                >
                                    Загрузка факса
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-layout row wrap>
                                            <!--<LinksList :value="test">-->
                                            <!--<div slot="link" slot-scope="{ link, markBtn, value}">-->
                                            <!--&lt;!&ndash;<star-icon v-show="link.bookmarked"></star-icon>&ndash;&gt;-->
                                            <!--<a :href="link.href">-->
                                            <!--{{ link.title }}-->
                                            <!--</a>-->
                                            <!--<span>{{ value }}</span>-->
                                            <!--<button v-show="link.bookmarked" @click="markBtn(link)">Bookmark</button>-->
                                            <!--</div>-->
                                            <!--</LinksList>-->

                                            <v-flex xs12 sm12 md12>
                                                <DatePicker
                                                    :value.sync="dateOfDeparture"
                                                    :errorDate="faxData.errorDate"
                                                    :datePickerSettings="$_dateOfDeparturePickerSettings"
                                                />
                                            </v-flex>

                                            <v-flex xs12 sm12 md12>
                                                <v-text-field
                                                    v-model.trim="faxData.faxName"
                                                    :error-messages="checkError('faxName')"
                                                    clearable
                                                    label="Имя файла"
                                                    autofocus
                                                ></v-text-field>
                                            </v-flex>

                                            <v-flex xs12 sm6 md6>
                                                <ButtonUploadFile
                                                    :file.sync="fileForUpload"
                                                    :buttonUploadFileSettings="$_buttonUploadFileSettings"
                                                />
                                            </v-flex>

                                            <v-flex xs12 sm6 md6>
                                                <v-text-field
                                                    v-model="faxData.fileInfo"
                                                    :error-messages="checkError('uploadedFile')"
                                                    label="Имя загружаемого файла"
                                                    readonly
                                                ></v-text-field>
                                            </v-flex>

                                        </v-layout>
                                    </v-container>

                                </v-card-text>

                                <v-divider></v-divider>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <DefaultButton
                                        @clickButton="cancelUploadFile"
                                        :defaultButtonSettings="$_defaultButtonCancelSettings"
                                    />

                                    <DefaultButton
                                        :defaultButtonSettings="$_defaultButtonSendFileToServerSettings"
                                        :disabledButton="validSensor"
                                        @clickButton="sendFileToServer"
                                    />
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </div>
                </template>
            </v-toolbar>

            <v-data-table
                v-model="selected"
                :headers="headers"
                :search="search"
                :items="faxesData"
                :pagination.sync="pagination"
                select-all
                item-key="id"
                disable-initial-sort
                class="elevation-1"
            >
                <template slot="headers" slot-scope="props">
                    <tr>
                        <th>
                            <v-checkbox
                                :input-value="props.all"
                                :indeterminate="props.indeterminate"
                                primary
                                hide-details
                                @click.stop="toggleAll"
                            ></v-checkbox>
                        </th>
                        <th
                            v-for="header in props.headers"
                            :key="header.text"
                            :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '']"
                            @click="changeSort(header.value)"
                        >
                            <v-icon small>arrow_upward</v-icon>
                            {{ header.text }}
                        </th>
                    </tr>
                </template>
                <template slot="items" slot-scope="props">
                    <tr
                        :class="props.item.uploaded_to_table_cargos ? 'tr_green__bg' : 'tr_red__bg'"
                        :active="props.selected"
                        @click="props.selected = !props.selected"
                    >
                        <td>
                            <v-checkbox
                                :input-value="props.selected"
                                :active="props.selected"
                                primary
                                hide-details
                            ></v-checkbox>
                        </td>
                        <td class="text-xs-center">
                            <router-link :to="{name: 'home-faxes-counted', params: {faxID: props.item.id, faxName: props.item.fax_name}}">
                                {{ props.item.fax_name }}
                            </router-link>
                        </td>
                        <td class="text-xs-center">{{ props.item.date_departure }}</td>
                        <td class="text-xs-center">{{ props.item.uploaded_to_table_cargos_date ?
                            props.item.uploaded_to_table_cargos_date : '----' }}
                        </td>
                        <td class="text-xs-center">{{ props.item.uploaded_to_table_cargos ? 'Да': 'Нет' }}</td>
                        <td class="text-xs-center">{{ props.item.created_at }}</td>
                    </tr>
                </template>

                <template slot="no-results">
                    <v-alert :value="true" color="error" icon="warning">
                        Поиск по "{{ search }}" не дал результатов.
                    </v-alert>
                </template>

            </v-data-table>
        </v-container>
    </div>
</template>

<script>
    // import { mapGetters } from 'vuex';
    import axios from 'axios';
    import { today } from '~/utils/dates';
    import checkErrorMixin from '~/mixins/checkError';
    // import Swal from 'sweetalert2';

    export default {
        name: 'Faxes',
        components: {
            Search: () => import('~/components/Search'),
            ButtonUploadFile: () => import('~/components/Buttons/ButtonUploadFile'),
            DefaultButton: () => import('~/components/Buttons/DefaultButton'),
            DatePicker: () => import('~/components/Pickers/DatePicker'),
        },
        mixins: [checkErrorMixin],
        data () {
            this.$_buttonUploadFileSettings = {
                color: 'blue-grey',
                class: 'white--text',
                icon: 'cloud_upload',
            };

            this.$_defaultButtonCancelSettings = {
                color: 'red',
                flat: 'flat',
                outline: 'outline',
                title: 'Отмена',
            };

            this.$_defaultButtonSendFileToServerSettings = {
                color: 'primary',
                flat: 'flat',
                outline: 'outline',
                title: 'Загрузить',
            };

            this.$_defaultButtonAddFaxSettings = {
                color: 'green',
                dark: true,
                title: 'Добавить',
            };

            this.$_dateOfDeparturePickerSettings = {
                label: 'Дата выезда',
            };

            return {
                test: 'SFDGGDJJD',
                faxData: {
                    faxName: '',
                    fileInfo: '',
                    errorDate: '',
                },
                dateOfDeparture: today(),
                fileForUpload: new FormData(),
                search: '',
                dialog: false,
                validSensor: false,
                pagination: {
                    sortBy: 'name',
                },
                selected: [],
                headers: [
                    { text: 'Название факса', align: 'center', value: 'fax_name' },
                    { text: 'Дата выезда', align: 'center', value: 'date_departure' },
                    { text: 'Дата загрузки в базу', align: 'center', value: 'uploaded_to_table_cargos_date' },
                    { text: 'Загружен в базу', align: 'center', value: 'uploaded_to_table_cargos' },
                    { text: 'Дата добавления', align: 'center', value: 'created_at' },
                ],
            };
        },
        watch: {
            fileForUpload (formData) {
                this.setFileInfo(formData);
            },
        },
        asyncData () {
            return axios.get('faxes')
                .then(res => ({ faxesData: res.data.faxesData }));
        },
        methods: {
            addPropsToFormData () {
                this.fileForUpload.append('dateOfDeparture', this.dateOfDeparture);
                this.fileForUpload.append('faxName', this.faxData.faxName);
            },
            setFileInfo (formData) {
                this.faxData.fileInfo = `${formData.get('fileName')} [${_.round(formData.get('fileSize') / 1024)} кб]`;
            },
            toggleAll () {
                if (this.selected.length) {
                    this.selected = [];
                } else {
                    this.selected = this.faxesData.slice();
                }
            },
            changeSort (column) {
                if (this.pagination.sortBy === column) {
                    this.pagination.descending = !this.pagination.descending;
                } else {
                    this.pagination.sortBy = column;
                    this.pagination.descending = false;
                }
            },
            cancelUploadFile () {
                this.openCloseDialog(false);
                this.clearData();
                this.changeErrors({});
            },
            async sendFileToServer () {
                this.loadingDisabledBtn(true);

                if (this.validationData()) {
                    this.addPropsToFormData();

                    await axios.post('faxes/storeFax', this.fileForUpload).then((response) => {
                        const { data } = response;
                        const { status } = data;
                        if (status) {
                            const { fax } = data;
                            console.log('DATA', data);
                            this.addFaxToTableAfterSaveOnServer(fax);

                            this.openCloseDialog(false);
                            this.clearData();

                            this.$snotify.success('Факс успешно добавлен.', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                        } else {
                            const { error, exception, row } = data;
                            this.$snotify.warning(error, {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                            console.error(`Ошибка - ${error}, Строка записи - ${row}, Exception - ${_.last(exception.errorInfo)}`);
                        }
                    }).catch((errors) => {
                        this.changeErrors(errors.response.data.errors);
                    });
                }
                this.loadingDisabledBtn(false);
            },
            openCloseDialog (bool) {
                this.dialog = bool;
            },
            loadingDisabledBtn (bool) {
                this.validSensor = bool;
            },
            addFaxToTableAfterSaveOnServer (fax) {
                this.faxesData.unshift(fax);
            },
            validationData () {
                const errorsObj = {};
                if (!this.faxData.fileInfo) {
                    errorsObj.fileInfo = 'Добавте файл.';
                } else {
                    delete errorsObj.fileInfo;
                }

                if (!this.faxData.faxName) {
                    errorsObj.faxName = 'Введите имя файла.';
                } else {
                    delete errorsObj.faxName;
                }

                if (!this.dateOfDeparture) {
                    this.faxData.errorDate = 'Выберите дату выезда';
                } else {
                    this.faxData.errorDate = '';
                }

                this.changeErrors(errorsObj);

                return _.isEmpty(errorsObj) && this.dateOfDeparture;
            },
            clearData () {
                _.forIn(this.faxData, (item, key, obj) => {
                    obj[key] = '';
                });
            },
        },
    };

</script>
