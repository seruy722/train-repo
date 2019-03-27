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
                <v-spacer></v-spacer>
                <!--Диалог загрузки факса-->
                <DialogAddFax
                    :click-savesin-edit-dialog="clickSaveInEditDialog"
                />
                <!--<template>-->
                <!--<div class="text-xs-center">-->
                <!--<RectangleBtn-->
                <!--v-show="!clickSaveInEditDialog"-->
                <!--:title="'Добавить'"-->
                <!--@clickRectangleBtn="openCloseDialog(true)"-->
                <!--/>-->

                <!--<RectangleBtn-->
                <!--v-show="clickSaveInEditDialog"-->
                <!--@clickRectangleBtn="updateFaxData"-->
                <!--/>-->

                <!--<v-dialog-->
                <!--v-model="dialog"-->
                <!--persistent-->
                <!--max-width="500"-->
                <!--&gt;-->
                <!--<v-card>-->
                <!--<v-card-title-->
                <!--class="headline grey lighten-2"-->
                <!--primary-title-->
                <!--&gt;-->
                <!--Загрузка факса-->
                <!--</v-card-title>-->

                <!--<v-card-text>-->
                <!--<v-container>-->
                <!--<v-layout row wrap>-->
                <!--<v-flex xs12 sm12 md12>-->
                <!--<DatePicker-->
                <!--:value.sync="dateOfDeparture"-->
                <!--:errorDate="faxData.errorDate"-->
                <!--:datePickerSettings="$_dateOfDeparturePickerSettings"-->
                <!--/>-->
                <!--</v-flex>-->

                <!--<v-flex xs12 sm12 md12>-->
                <!--<v-text-field-->
                <!--v-model.trim="faxData.faxName"-->
                <!--:error-messages="checkError('faxName')"-->
                <!--clearable-->
                <!--label="Имя файла"-->
                <!--autofocus-->
                <!--&gt;</v-text-field>-->
                <!--</v-flex>-->

                <!--<v-flex xs12 sm6 md6>-->
                <!--<ButtonUploadFile-->
                <!--:file.sync="fileForUpload"-->
                <!--:buttonUploadFileSettings="$_buttonUploadFileSettings"-->
                <!--/>-->
                <!--</v-flex>-->

                <!--<v-flex xs12 sm6 md6>-->
                <!--<v-text-field-->
                <!--v-model="faxData.fileInfo"-->
                <!--:error-messages="checkError('uploadedFile')"-->
                <!--label="Имя загружаемого файла"-->
                <!--readonly-->
                <!--&gt;</v-text-field>-->
                <!--</v-flex>-->

                <!--</v-layout>-->
                <!--</v-container>-->

                <!--</v-card-text>-->

                <!--<v-divider></v-divider>-->

                <!--<v-card-actions>-->
                <!--<v-spacer></v-spacer>-->

                <!--<DefaultButton-->
                <!--@clickButton="cancelUploadFile"-->
                <!--:defaultButtonSettings="$_defaultButtonCancelSettings"-->
                <!--/>-->

                <!--<DefaultButton-->
                <!--:defaultButtonSettings="$_defaultButtonSendFileToServerSettings"-->
                <!--:disabledButton="validSensor"-->
                <!--@clickButton="sendFileToServer"-->
                <!--/>-->
                <!--</v-card-actions>-->
                <!--</v-card>-->
                <!--</v-dialog>-->
                <!--</div>-->
                <!--</template>-->
            </v-toolbar>

            <v-data-table
                v-model="selected"
                :headers="mainTableHeaders"
                :search="search"
                :items="faxes"
                :pagination.sync="pagination"
                select-all
                item-key="id"
                disable-initial-sort
                class="elevation-1"
            >
                <template v-slot:headers="props">
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

                <template v-slot:items="props">
                    <tr
                        :class="props.item.uploaded_to_table_cargos_date ? 'tr_green__bg' : 'tr_red__bg'"
                        class="table__tr_show_hide_tr_control_panel"
                        :active="props.selected"
                        @click.stop="props.selected = !props.selected"
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
                            <router-link
                                :to="{name: 'home-faxes-counted', params: {faxID: props.item.id, faxName: props.item.fax_name}}"
                                @click.native="$event.stopImmediatePropagation()">
                                {{ props.item.fax_name | upperFirst }}
                            </router-link>
                        </td>
                        <!--<td class="text-xs-center">{{ props.item.date_departure | formatDate }}</td>-->
                        <td class="text-xs-center">{{ props.item.uploaded_to_table_cargos_date | formatDate }}
                        </td>
                        <td class="text-xs-center">{{ props.item.air_or_car | convertBoolToAirOrCar }}</td>
                        <!--<td class="text-xs-center">{{ props.item.created_at | formatDate }}</td>-->
                        <ControlPanelFaxes
                            :item="props.item"
                            :selected="selected"
                            :destroy="true"
                            :edit="true"
                            :expandProps="props"
                            :download="true"
                            class="faxes_table_control_panel"
                        />
                    </tr>
                </template>
                <!--Expand-->
                <template v-slot:expand="props">
                    <div class="ml-4 mb-5">
                        <v-data-table
                            :headers="expandTableHeaders"
                            :items="[props.item]"
                        >
                            <template v-slot:items="props">
                                <tr>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.fax_name"
                                            large
                                            lazy
                                            persistent
                                            @save="updateFaxDataEditDialog(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.fax_name | upperFirst }}</div>
                                            <template v-slot:input>
                                                <div class="mt-3 title">Update Iron</div>
                                            </template>
                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.fax_name"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
                                    </td>

                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.date_departure"
                                            large
                                            lazy
                                            persistent
                                            @save="updateFaxDataEditDialog(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.date_departure | formatDate }}</div>
                                            <template v-slot:input>
                                                <div class="mt-3 title">Update Iron</div>
                                            </template>
                                            <template v-slot:input>
                                                <DatePicker
                                                    :value.sync="props.item.date_departure"
                                                    :errorDate="faxData.errorDate"
                                                    :datePickerSettings="$_dateOfDeparturePickerSettings"
                                                />
                                            </template>
                                        </v-edit-dialog>
                                    </td>

                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.uploaded_to_table_cargos_date"
                                            large
                                            lazy
                                            persistent
                                            @save="updateFaxDataEditDialog(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.uploaded_to_table_cargos_date | formatDate }}</div>
                                            <template v-slot:input>
                                                <div class="mt-3 title">Update Iron</div>
                                            </template>
                                            <template v-slot:input>
                                                <DatePicker
                                                    :value.sync="props.item.uploaded_to_table_cargos_date"
                                                    :errorDate="faxData.errorDate"
                                                    :datePickerSettings="$_dateOfDeparturePickerSettings"
                                                />
                                                <!--<v-text-field-->
                                                <!--v-model="props.item.uploaded_to_table_cargos_date"-->
                                                <!--label="Edit"-->
                                                <!--single-line-->
                                                <!--counter-->
                                                <!--autofocus-->
                                                <!--&gt;</v-text-field>-->
                                            </template>
                                        </v-edit-dialog>
                                    </td>

                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.air_or_car"
                                            large
                                            lazy
                                            persistent
                                            @save="updateFaxDataEditDialog(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.air_or_car | convertBoolToAirOrCar }}</div>
                                            <template v-slot:input>
                                                <div class="mt-3 title">Update Iron</div>
                                            </template>
                                            <template v-slot:input>
                                                <v-select
                                                    v-model.number="props.item.air_or_car"
                                                    :items="airOrCarList"
                                                    type="number"
                                                    label="Транспорт"
                                                ></v-select>
                                            </template>
                                        </v-edit-dialog>
                                    </td>

                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.created_at"
                                            large
                                            lazy
                                            persistent
                                            @save="updateFaxDataEditDialog(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.created_at | formatDate }}</div>
                                            <template v-slot:input>
                                                <div class="mt-3 title">Update Iron</div>
                                            </template>
                                            <template v-slot:input>
                                                <DatePicker
                                                    :value.sync="props.item.created_at"
                                                    :errorDate="faxData.errorDate"
                                                    :datePickerSettings="$_dateOfDeparturePickerSettings"
                                                />
                                                <!--<v-text-field-->
                                                <!--v-model="props.item.uploaded_to_table_cargos_date"-->
                                                <!--label="Edit"-->
                                                <!--single-line-->
                                                <!--counter-->
                                                <!--autofocus-->
                                                <!--&gt;</v-text-field>-->
                                            </template>
                                        </v-edit-dialog>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>

                        <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
                            <RectangleBtn
                                v-show="clickSaveInEditDialog"
                                :color="'cyan'"
                                @clickRectangleBtn="updateFaxData"
                            />

                            <v-spacer></v-spacer>

                            <RectangleBtn
                                v-show="clickSaveInEditDialog"
                                :title="'Закрыть'"
                                :color="'red'"
                                @clickRectangleBtn="snack = false"
                            />
                        </v-snackbar>
                    </div>
                </template>

                <template v-slot:no-results>
                    <v-alert :value="true" color="error" icon="warning">
                        Поиск по "{{ search }}" не дал результатов.
                    </v-alert>
                </template>

            </v-data-table>
        </v-container>
    </div>
</template>

<script>
    import axios from 'axios';
    import { today, needFormatDate } from '~/utils/dates';
    import checkErrorMixin from '~/mixins/checkError';
    import { mapGetters } from 'vuex';

    export default {
        name: 'Faxes',
        filters: {
            formatDate (value) {
                return needFormatDate(value) || '00-00-0000';
            },
            convertBoolToAirOrCar (value) {
                return value ? 'Авиа' : 'Машина';
            },
            upperFirst (value) {
                return _.upperFirst(value);
            },
        },
        components: {
            Search: () => import('~/components/Search'),
            ButtonUploadFile: () => import('~/components/Buttons/ButtonUploadFile'),
            DefaultButton: () => import('~/components/Buttons/DefaultButton'),
            DatePicker: () => import('~/components/Pickers/DatePicker'),
            ControlPanelFaxes: () => import('~/components/Table/ControlPanelFaxes'),
            RectangleBtn: () => import('~/components/Buttons/RectangleBtn'),
            DialogAddFax: () => import('~/components/Dialogs/DialogAddFax'),
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
                label: 'Дата отправки',
            };

            this.expandTableHeaders = [
                { text: 'Название факса', sortable: false, align: 'center', value: 'fax_name' },
                { text: 'Дата отправки', sortable: false, align: 'center', value: 'date_departure' },
                {
                    text: 'Дата загрузки в базу',
                    sortable: false,
                    align: 'center',
                    value: 'uploaded_to_table_cargos_date',
                },
                { text: 'Транспорт', sortable: false, align: 'center', value: 'air_or_car' },
                { text: 'Дата добавления', sortable: false, align: 'center', value: 'created_at' },
            ];

            this.mainTableHeaders = [
                { text: 'Название факса', align: 'center', value: 'fax_name' },
                {
                    text: 'Дата загрузки в базу',
                    align: 'center',
                    value: 'uploaded_to_table_cargos_date',
                },
                { text: 'Транспорт', align: 'center', value: 'air_or_car' },
            ];

            return {
                airOrCarList: [0, 1],
                clickSaveInEditDialog: false,
                snack: false,
                snackColor: '',
                snackText: '',
                expand: false,
                faxData: {
                    faxName: '',
                    fileInfo: '',
                    errorDate: '',
                },
                faxes: [],
                dateOfDeparture: today(),
                fileForUpload: new FormData(),
                search: '',
                dialog: false,
                validSensor: false,
                pagination: {
                    sortBy: 'name',
                },
                selected: [],
            };
        },
        computed: {
            ...mapGetters({
                getFaxes: 'fax/getFaxes',
            }),
        },
        watch: {
            fileForUpload (formData) {
                this.setFileInfo(formData);
            },
            getFaxes () {
                this.cloneFaxData();
            },
        },
        async fetch ({ store }) {
            try {
                if (_.isEmpty(store.getters['fax/getFaxes'])) {
                    //     Запрос данных всех факсов
                    const { data } = await axios.get('faxes');
                    const { faxesData = [] } = data;
                    // console.log('allFaxes', faxesData);

                    store.dispatch('fax/setFaxes', faxesData);
                }
            } catch (e) {
                console.error(`Произошла ошибка при запросе всех факсов - ${e}`);
            } finally {
                console.log('Completed request for get faxes.');
            }
        },
        created () {
            this.cloneFaxData();
        },
        methods: {
            cloneFaxData () {
                this.faxes = _.cloneDeep(this.getFaxes);
            },
            updateFaxData () {
                this.clickSaveInEditDialog = false;
                this.snack = false;
                console.log('UPDATE', this.faxes);
            },
            updateFaxDataEditDialog () {
                this.clickSaveInEditDialog = true;

                this.snack = true;
                this.snackColor = 'primary';
                this.snackText = 'Сохранено';
            },
            cancel () {

            },
            close () {
                this.clickSaveInEditDialog = true;
            },
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
                    this.selected = this.getFaxes.slice();
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

                    try {
                        const { data } = await axios.post('faxes/storeFax', this.fileForUpload);
                        // const { data } = response;
                        const { status, fax = [] } = data;
                        if (status) {
                            // console.log('DATA', data);
                            this.$store.dispatch('fax/addFax', fax);
                            this.openCloseDialog(false);
                            this.clearData();

                            this.$snotify.success('Факс успешно добавлен.', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                        }
                    } catch (e) {
                        console.error(`Произошла ошибка при загрузки факса на сервер - ${e}`);
                    } finally {
                        console.log('Completed request to upload fax on server.');
                    }
                }
                this.loadingDisabledBtn(false);
            },
            openCloseDialog (bool) {
                this.dialog = bool;
            },
            loadingDisabledBtn (bool) {
                this.validSensor = bool;
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
<style lang="scss">
    .table__tr_show_hide_tr_control_panel {
        position: relative;

        .faxes_table_control_panel {
            position: absolute;
            margin-top: 10px;
            right: 10%;
            display: none;
        }

        &:hover {
            .faxes_table_control_panel {
                display: block;
            }
        }
    }
</style>
