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
                    :click-save-in-edit-dialog="clickSaveInEditDialog"
                />
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
                                                    :date-picker-settings="$_dateOfDeparturePickerSettings"
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
                                                <div class="mt-3 title">Редактирование</div>
                                            </template>
                                            <template v-slot:input>
                                                <DatePicker
                                                    :value.sync="props.item.uploaded_to_table_cargos_date"
                                                    :date-picker-settings="$_dateOfDeparturePickerSettings"
                                                />
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
                                                <div class="mt-3 title">Редактирование</div>
                                            </template>
                                            <template v-slot:input>
                                                <v-select
                                                    v-model.number="props.item.air_or_car"
                                                    :items="transportItems"
                                                    item-text="title"
                                                    item-value="id"
                                                    hide-selected
                                                    type="number"
                                                    label="Транспорт"
                                                    single-line
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
                                                    :date-picker-settings="$_dateOfDeparturePickerSettings"
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
    import { needFormatDate } from '~/utils/dates';
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
            DatePicker: () => import('~/components/Pickers/DatePicker'),
            ControlPanelFaxes: () => import('~/components/Table/ControlPanelFaxes'),
            RectangleBtn: () => import('~/components/Buttons/RectangleBtn'),
            DialogAddFax: () => import('~/components/Dialogs/DialogAddFax'),
        },
        data () {
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
                clickSaveInEditDialog: false,
                snack: false,
                snackColor: '',
                snackText: '',
                expand: false,
                faxes: [],
                search: '',
                pagination: {
                    sortBy: 'name',
                },
                selected: [],
            };
        },
        computed: {
            ...mapGetters({
                getFaxes: 'fax/getFaxes',
                transportItems: 'settings/transportItems',
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
                    const { faxesData = [], date = null } = data;

                    console.log('date', date);

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
            },
            addItemToSendDataArrayForUpdate (item) {
                const newItem = _.clone(item);
                console.log('newItem', newItem);
                // delete newItem.clientItemsArray;
                // const itemIndex = _.findIndex(this.sendDataToUpdate, { id: newItem.id });
                //
                // if (itemIndex !== -1) {
                //     this.sendDataToUpdate.splice(itemIndex, 1, newItem);
                // } else {
                //     this.sendDataToUpdate.push(newItem);
                // }
            },
            updateFaxDataEditDialog (item) {
                this.clickSaveInEditDialog = true;
                this.snack = true;
                this.snackColor = 'primary';
                this.snackText = 'Сохранить';

                this.addItemToSendDataArrayForUpdate(item);
            },
            cancel () {

            },
            close () {
                this.clickSaveInEditDialog = true;
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
        },
    };

</script>
<style lang="scss">
    .table__tr_show_hide_tr_control_panel {
        position: relative;

        .faxes_table_control_panel {
            position: absolute;
            right: 12%;
            display: none;
        }

        &:hover {
            .faxes_table_control_panel {
                display: block;
            }
        }
    }
</style>
