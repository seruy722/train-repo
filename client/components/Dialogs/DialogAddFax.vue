<template>
    <div
        data-vue-component-name="DialogAddFax"
    >
        <template>
            <div class="text-xs-center">
                <RectangleBtn
                    v-show="!clickSaveInEditDialog"
                    :title="'Добавить'"
                    @clickRectangleBtn="openCloseDialog(true)"
                />

                <RectangleBtn
                    v-show="clickSaveInEditDialog"
                    @clickRectangleBtn="updateFaxData"
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
                                    <v-flex xs12 sm12 md12>
                                        <DatePicker
                                            :value.sync="faxData.dateOfDeparture"
                                            :error-date="faxData.errorDate"
                                            :date-picker-settings="$_dateOfDeparturePickerSettings"
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

                                    <v-flex xs12 sm12 md12>
                                        <!--<v-switch-->
                                        <!--v-model="switchTransport"-->
                                        <!--:label="`Транспорт: ${switchTransport? 'Авиа': 'Машина'}`"-->
                                        <!--&gt;</v-switch>-->
                                        <v-select
                                            v-model="faxData.selectedTransportItem"
                                            :items="transportItems"
                                            :error-messages="checkError('selectedTransportItem')"
                                            item-text="title"
                                            item-value="id"
                                            label="Транспорт"
                                            return-object
                                            single-line
                                        ></v-select>
                                    </v-flex>

                                    <v-flex xs12 sm6 md6>
                                        <ButtonUploadFile
                                            :file.sync="fileForUpload"
                                            :button-upload-file-settings="$_buttonUploadFileSettings"
                                        />
                                    </v-flex>

                                    <v-flex xs12 sm6 md6>
                                        <v-text-field
                                            v-model="faxData.fileInfo"
                                            :error-messages="checkError(['fileInfo', 'uploadedFile'])"
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
                                :default-button-settings="$_defaultButtonCancelSettings"
                                @clickButton="cancelUploadFile"
                            />

                            <DefaultButton
                                :default-button-settings="$_defaultButtonSendFileToServerSettings"
                                :disabled-button="validSensor"
                                @clickButton="sendFileToServer"
                            />
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </div>
        </template>
    </div>
</template>

<script>
    import axios from 'axios';
    import checkErrorMixin from '~/mixins/checkError';
    import { today } from '~/utils/dates';

    export default {
        name: 'DialogAddFax',
        components: {
            ButtonUploadFile: () => import('~/components/Buttons/ButtonUploadFile'),
            DefaultButton: () => import('~/components/Buttons/DefaultButton'),
            DatePicker: () => import('~/components/Pickers/DatePicker'),
            RectangleBtn: () => import('~/components/Buttons/RectangleBtn'),
        },
        mixins: [checkErrorMixin],
        props: {
            clickSaveInEditDialog: {
                type: Boolean,
                default: false,
            },
        },
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

            return {
                transportItems: [
                    {
                        id: 0,
                        title: 'Машина',
                    }, {
                        id: 1,
                        title: 'Авиа',
                    },
                ],
                // dateOfDeparture: today(),
                fileForUpload: new FormData(),
                dialog: false,
                validSensor: false,
                faxData: {
                    faxName: '',
                    fileInfo: '',
                    errorDate: '',
                    selectedTransportItem: {},
                    dateOfDeparture: today(),
                },
            };
        },
        watch: {
            fileForUpload (formData) {
                this.setFileInfo(formData);
            },
        },
        methods: {
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
            updateFaxData () {

            },
            cancelUploadFile () {
                this.openCloseDialog(false);
                this.clearData();
                this.changeErrors({});
            },
            addPropsToFormData () {
                this.fileForUpload.append('dateOfDeparture', this.faxData.dateOfDeparture);
                this.fileForUpload.append('transport', this.faxData.selectedTransportItem.id);
                this.fileForUpload.append('faxName', this.faxData.faxName);
            },
            setFileInfo (formData) {
                this.faxData.fileInfo = `${formData.get('fileName')} [${_.round(formData.get('fileSize') / 1024)} кб]`;
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

                if (!this.faxData.dateOfDeparture) {
                    this.faxData.errorDate = 'Выберите дату отправки';
                } else {
                    this.faxData.errorDate = '';
                }

                if (_.isEmpty(this.faxData.selectedTransportItem)) {
                    errorsObj.selectedTransportItem = 'Выберите транспорт';
                } else {
                    delete errorsObj.selectedTransportItem;
                }
                console.log(this.faxData);
                this.changeErrors(errorsObj);

                return _.isEmpty(errorsObj);
            },
            clearData () {
                _.forIn(this.faxData, (item, key, obj) => {
                    obj[key] = '';
                });
            },
        },
    };
</script>
