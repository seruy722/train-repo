<template>

    <div
        class="main"
        data-vue-component-name="FaxCounted"
    >
        <template>
            <!--Диалог настроки видимости столбцов-->
            <v-layout row justify-center>
                <v-dialog v-model.lazy="dialogSelectedColumn" scrollable max-width="300px">
                    <v-card>
                        <v-card-title>Видимость столбцов</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text style="height: 300px;">
                            <div v-for=" item in $_mainTableHeaders" :key="item.text">
                                <v-switch v-model.lazy="selectedColumn" :label="item.text"
                                          :value="item.text"></v-switch>
                            </div>
                        </v-card-text>
                        <v-divider></v-divider>
                    </v-card>
                </v-dialog>
            </v-layout>

            <v-toolbar>
                <v-toolbar-title>
                    <span class="font-weight-bold">{{ getFaxName }}</span>
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-toolbar-items>
                    <v-btn
                        v-show="updateTableData"
                        color="green"
                        dark
                        @click="updateFaxData"
                    >
                        Сохранить
                    </v-btn>
                    <v-menu bottom left>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                v-on="on"
                                dark
                                icon
                            >
                                <v-icon color="blue">menu</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-tile
                                v-for="(item, i) in $_items"
                                :key="i"
                                @click="selectMenuItem(item)"
                            >
                                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-toolbar-items>
            </v-toolbar>

            <v-data-table
                v-model="selected"
                :headers="$_mainTableHeaders"
                :items="faxData"
                :expand="expand"
                :loading="isLoadedDataToMainTable"
                item-key="name"
                class="elevation-1"
            >

                <template v-slot:items="props">
                    <tr
                        :class="{['table__tr-bold_text table__tr-bg_color']: props.item.brand}"
                        :active="props.selected"
                        class="table__tr_show_hide_tr_control_panel"
                        @click="openCloseExpandedPanel(props)"
                    >
                        <td
                            :class="{'table__td-hide': !selectedColumn.includes('Клиент')}"
                            class="text-xs-center"
                        >
                            <v-badge v-if="props.item.brand" class="table__td-badge" right>
                                <template v-slot:badge>
                                    <span>Бренд</span>
                                </template>
                                <span>{{ props.item.name | clientFormat }}</span>
                            </v-badge>
                            <span v-if="!props.item.brand">{{ props.item.name | clientFormat }}</span>
                        </td>

                        <td
                            :class="{'table__td-hide': !selectedColumn.includes('Мест')}"
                            class="text-xs-center"
                        >
                            {{ props.item.place | numFormat}}
                        </td>

                        <td
                            :class="{'table__td-hide': !selectedColumn.includes('Вес')}"
                            class="text-xs-center"
                        >
                            {{ props.item.kg | numFormat }}
                        </td>

                        <td
                            :class="{'table__td-hide': !selectedColumn.includes('За кг')}"
                            class="text-xs-center table__td-color-text table__tr-bold_text"
                        >
                            <v-edit-dialog
                                :return-value.sync="props.item.for_kg"
                                large
                                lazy
                                persistent
                                @save="saveCommonPriceForClient(props.item, 'for_kg', props.item.for_kg)"
                                @cancel="cancelInCategoriesTable"

                                @close="closeInCategoriesTable"
                            >
                                <div class="table__td-color-text table__tr-bold_text">{{ props.item.for_kg }}</div>

                                <template v-slot:input>
                                    <div class="mt-3 title">Цена за кг</div>
                                </template>

                                <template v-slot:input>
                                    <v-text-field
                                        v-model.number="props.item.for_kg"
                                        label="Edit"
                                        type="number"
                                        single-line
                                        counter
                                        autofocus
                                    ></v-text-field>
                                </template>

                            </v-edit-dialog>
                        </td>

                        <td
                            :class="{'table__td-hide': !selectedColumn.includes('За место')}"
                            class="text-xs-center table__td-color-text table__tr-bold_text"
                        >
                            <v-edit-dialog
                                :return-value.sync="props.item.for_place"
                                large
                                lazy
                                persistent
                                @save="saveCommonPriceForClient(props.item, 'for_place', props.item.for_place)"
                                @cancel="cancelInCategoriesTable"

                                @close="closeInCategoriesTable"
                            >
                                <div class="table__td-color-text table__tr-bold_text">{{ props.item.for_place }}</div>

                                <template v-slot:input>
                                    <div class="mt-3 title">Цена за место</div>
                                </template>

                                <template v-slot:input>
                                    <v-text-field
                                        v-model.number="props.item.for_place"
                                        label="Edit"
                                        type="number"
                                        single-line
                                        counter
                                        autofocus
                                    ></v-text-field>
                                </template>

                            </v-edit-dialog>
                        </td>

                        <td
                            :class="{'table__td-hide': !selectedColumn.includes('Сумма')}"
                            class="text-xs-center table__tr-bold_text"
                        >
                            {{ props.item.sum | numFormat }}
                        </td>
                        <!--Контроль панель для груп записей таблицы-->
                        <ControlPanelFaxEntries
                            :item="props.item"
                            :move="true"
                            :destroy="true"
                            :many="true"
                            class="fax_table_control_panel"
                        />
                    </tr>
                </template>

                <!--Expand panel-->
                <template v-slot:expand="props">
                    <div class="ml-4 mb-5">
                        <v-data-table
                            :headers="$_subTableHeaders"
                            :items="props.item.clientItemsArray"
                            hide-actions
                            class="elevation-1 table__tr-border_color table__thead-bg_color"
                        >
                            <template v-slot:items="props">
                                <tr
                                    class="table__tr_show_hide_tr_control_panel"
                                    :class="{['table__tr-bold_text table__tr-bg_color']: props.item.brand}"
                                >
                                    <td>
                                        <v-edit-dialog
                                            :return-value.sync="props.item.code"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.code }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Код</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.code"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td>
                                        <v-edit-dialog
                                            :return-value.sync="props.item.name"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                            @open="openClientsNamesEditDialog"
                                        >
                                            <div>{{ props.item.name | clientFormat }}</div>
                                            <template v-slot:input>
                                                <div class="mt-3 title">Клиент</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-combobox
                                                    v-model="props.item.name"
                                                    :items="getClientsNames"
                                                    :rules="[v => !!v || 'Обьязательное поле.']"
                                                    :loading="selectLoading"
                                                    label="Клиент"
                                                    hide-selected
                                                ></v-combobox>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.place"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.place | numFormat }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Мест</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model.number="props.item.place"
                                                    label="Edit"
                                                    type="number"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.kg"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                        >
                                            <div>{{ props.item.kg | numFormat }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Вес</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model.number="props.item.kg"
                                                    label="Edit"
                                                    type="number"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value="props.item.for_kg"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"

                                            @close="close"
                                        >
                                            <div class="table__td-color-text table__tr-bold_text">
                                                {{ props.item.for_kg | numFormat }}
                                            </div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Цена за кг</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model.number="props.item.for_kg"
                                                    label="Edit"
                                                    type="number"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>

                                                <v-checkbox
                                                    v-model="props.item.changeValues.for_kg"
                                                    label="Заменить"
                                                ></v-checkbox>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.for_place"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"

                                            @close="close"
                                        >
                                            <div class="table__td-color-text table__tr-bold_text">
                                                {{ props.item.for_place | numFormat }}
                                            </div>
                                            <template v-slot:input>
                                                <div class="mt-3 title">Цена за место</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model.number="props.item.for_place"
                                                    label="Edit"
                                                    type="number"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>

                                                <v-checkbox
                                                    v-model="props.item.changeValues.for_place"
                                                    label="Заменить"
                                                ></v-checkbox>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.category_name"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"
                                            @close="close"
                                            @open="openCategoriesNamesEditDialog"
                                        >
                                            <div>{{ props.item.category_name }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Категория</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-select
                                                    v-model="props.item.category_name"
                                                    :items="getCategoriesNames"
                                                    :loading="selectLoading"
                                                    label="Категория"
                                                ></v-select>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.brand"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"

                                            @close="close"
                                        >
                                            <div>{{ props.item.brand | convertCommonItems }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Бренд</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-select
                                                    v-model.number="props.item.brand"
                                                    :items="commonItems"
                                                    item-text="title"
                                                    item-value="id"
                                                    type="number"
                                                    label="Бренд"
                                                ></v-select>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <td class="text-xs-center">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.shop"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"

                                            @close="close"
                                        >
                                            <div>{{ props.item.shop }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Магазин</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.shop"
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
                                            :return-value.sync="props.item.name_of_things"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"

                                            @close="close"
                                        >
                                            <div>{{ props.item.name_of_things }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Опись вложения</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.name_of_things"
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
                                            :return-value.sync="props.item.notation"
                                            large
                                            lazy
                                            persistent
                                            @save="changeItemInFaxDataTeble(props.item)"
                                            @cancel="cancel"

                                            @close="close"
                                        >
                                            <div>{{ props.item.notation }}</div>

                                            <template v-slot:input>
                                                <div class="mt-3 title">Примечания</div>
                                            </template>

                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model.trim="props.item.notation"
                                                    label="Примечания"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>

                                        </v-edit-dialog>
                                    </td>
                                    <!--Контроль панель для отдельных записей таблицы-->
                                    <ControlPanelFaxEntries
                                        :item="props.item"
                                        :move="true"
                                        :destroy="true"
                                        class="table_tr_control_panel"
                                    />
                                </tr>
                            </template>

                            <template v-slot:no-data>
                                <v-alert v-if="isLoadedDataToMainTable" :value="true" color="primary" icon="info">
                                    <span>Загрузка...</span>
                                </v-alert>

                                <v-alert v-else :value="true" color="error" icon="warning">
                                    <span>Таблица пустая</span>
                                </v-alert>
                            </template>

                        </v-data-table>

                        <v-snackbar v-model.lazy="snack" :timeout="6000" :color="snackColor">
                            <!--<v-badge left color="purple">-->
                                <!--<template v-slot:badge>-->
                                    <!--<span>{{ counter }}</span>-->
                                <!--</template>-->
                            <!--</v-badge>-->

                            <v-btn
                                v-show="updateTableData"
                                color="cyan"
                                dark
                                @click="updateFaxData"
                            >
                                Сохранить
                            </v-btn>
                            <v-spacer></v-spacer>
                            <v-btn flat @click="snack = false">Закрыть</v-btn>
                        </v-snackbar>
                    </div>
                </template>
            </v-data-table>
        </template>

        <!--Таблица сводки по категориям-->
        <!--<div class="table_categories">-->
            <v-flex xs12 sm8 md6 class="table_categories">
            <v-data-table
                :headers="$_tableCategoriesHeaders"
                :items="tableCategoriesItems"
                class="elevation-1"
                hide-actions
                hide-headers
                disable-initial-sort
            >
                <template v-slot:items="props">
                    <tr :class="{'table__tr-bold_text': props.item.totalDataClass}">
                        <td>{{ props.item.category_name }}</td>
                        <td class="text-xs-center">{{ props.item.place }}</td>
                        <td class="text-xs-center">{{ props.item.kg }}</td>
                        <td class="text-xs-center">
                            <v-edit-dialog
                                :return-value.sync="props.item.for_kg"
                                large
                                lazy
                                persistent
                                @save="saveInCategoriesTable(props.item)"
                                @cancel="cancelInCategoriesTable"

                                @close="closeInCategoriesTable"
                            >
                                <div class="table__td-color-text table__tr-bold_text">{{ props.item.for_kg }}</div>

                                <template v-slot:input>
                                    <div class="mt-3 title">Цена за кг</div>
                                </template>

                                <template v-slot:input>
                                    <v-text-field
                                        v-model.number="props.item.for_kg"
                                        label="Edit"
                                        type="number"
                                        single-line
                                        counter
                                        autofocus
                                    ></v-text-field>
                                </template>

                            </v-edit-dialog>
                        </td>
                        <td
                            :class="{
                            'table__td_fax_difference_table_total_data_bg': props.item.totalDataClass === 'table__td_fax_difference_table_total_data_bg',
                            'table__td_categories_table_total_data': props.item.totalDataClass === 'table__td_categories_table_total_data',
                            'table__td_fax_table_total_data': props.item.totalDataClass === 'table__td_fax_table_total_data',
                            }"
                            class="text-xs-center"
                        >
                            {{ props.item.sum | numFormat }}
                        </td>
                    </tr>
                </template>
            </v-data-table>

            <v-btn
                v-show="needSaveTableCategoriesData"
                color="success"
                @click="saveDataCategoriesTable"
            >
                Сохранить
            </v-btn>
            </v-flex>
        </div>
    <!--</div>-->

</template>

<script>
    import axios from 'axios';
    import Cookies from 'js-cookie';
    import { numberFormat } from '~/utils';
    import { mapGetters } from 'vuex';

    export default {
        name: 'FaxCounted',
        components: {
            ControlPanelFaxEntries: () => import('~/components/Table/ControlPanelFaxEntries'),
        },
        filters: {
            numFormat (val) {
                return numberFormat(val) || 0;
            },
            clientFormat (val) {
                if (_.includes(val, '/')) {
                    const endIndex = val.indexOf('/');
                    return val.substr(endIndex + 1);
                }
                return val;
            },
        },
        data () {
            this.$_defaultButtonAddFaxSettings = {
                color: 'green',
                title: 'Сохранить',

            };

            this.$_totalFaxDataDefaultValues = {
                title: 'Данные по факсу',
                place: 0,
                kg: 0,
                sum: 0,
                totalDataClass: 'table__td_fax_table_total_data',
            };

            this.$_subTableHeaders = [
                { text: 'Код', align: 'center', sortable: false },
                { text: 'Клиент', align: 'center', sortable: false },
                { text: 'Мест', align: 'center', sortable: false },
                { text: 'Вес', align: 'center', sortable: false },
                { text: 'За кг', align: 'center', sortable: false },
                { text: 'За место', align: 'center', sortable: false },
                { text: 'Категория', align: 'center', sortable: false },
                { text: 'Бренд', align: 'center', sortable: false },
                { text: 'Магазин', align: 'center', sortable: false },
                { text: 'Опись вложения', align: 'center', sortable: false },
                { text: 'Примечания', align: 'center', sortable: false },
            ];

            this.$_tableCategoriesHeaders = [
                {
                    text: 'Категория',
                    align: 'center',
                    value: 'category_name',
                },
                { text: 'Мест', sortable: false, value: 'category_name' },
                { text: 'Вес', sortable: false, value: 'category_name' },
            ];

            this.$_mainTableHeaders = [
                { text: 'Клиент', align: 'center', value: 'name' },
                { text: 'Мест', align: 'center', value: 'place' },
                { text: 'Вес', align: 'center', value: 'kg' },
                { text: 'За кг', align: 'center', value: 'for_kg' },
                { text: 'За место', align: 'center', value: 'for_place' },
                { text: 'Сумма', align: 'center', value: 'sum' },
            ];

            this.$_totalFaxData = {
                title: 'Данные по факсу',
                place: 0,
                kg: 0,
                sum: 0,
                totalDataClass: 'table__td_fax_table_total_data',
            };

            this.$_totalCategoriesData = {
                kg: 0,
                place: 0,
                sum: 0,
                totalDataClass: 'table__td_categories_table_total_data',
            };

            this.$_items = [
                { id: 0, title: 'Скачать оригинал' },
                { id: 1, title: 'В excel' },
                { id: 2, title: 'Настройки' },
                { id: 3, title: 'Рассылка' },
            ];

            return {
                checkbox: false,
                // faxNames: [],
                selectLoading: true,
                dialog: false,
                counter: 6,
                isLoadedDataToMainTable: false,
                brandSelectItems: [
                    {
                        title: 'Да',
                        value: 1,
                    },
                    {
                        title: 'Нет',
                        value: 0,
                    },
                ],
                tableCategoriesItems: [],
                differenceTotalSum: {
                    sum: 0,
                    totalDataClass: 'table__td_fax_difference_table_total_data_bg',
                },
                faxData: [],
                moveEntries: {},
                sendDataToUpdate: [],
                updateTableData: false,
                needSaveTableCategoriesData: false,
                snack: false,
                snackColor: '',
                snackText: '',
                dialogSelectedColumn: false,
                selectedColumn: [],
                // Селект для таблицы чтобы работало активное поле
                selected: [],
                expand: false,
            };
        },
        computed: {
            ...mapGetters({
                getGroupedData: 'fax/getGroupedData',
                getTableCategoriesData: 'fax/getTableCategoriesData',
                getClientsNames: 'fax/getClientsNames',
                getCategoriesNames: 'fax/getCategoriesNames',
                getFaxData: 'fax/getFaxData',
                getFaxesNames: 'fax/getFaxesNames',
                commonItems: 'settings/commonItems',
            }),
            getFaxName () {
                return Cookies.get('faxName');
            },
        },
        watch: {
            selectedColumn (arrayVal) {
                const arrayMainTableHeadersText = _.map(this.$_mainTableHeaders, obj => obj.text);
                const diffArray = _.difference(arrayMainTableHeadersText, arrayVal);

                _.forEach(this.$_mainTableHeaders, (item) => {
                    if (_.includes(diffArray, item.text)) {
                        _.set(item, 'class', 'table__td-hide');
                    } else {
                        delete item.class;
                    }
                });
            },

            getFaxData () {
                // console.log('getFaxDataER');
                this.setFaxData();
                this.calcSumClientInFaxData(this.faxData);
                this.callStackForCategoriesTable();
                this.setChangeValues(this.faxData);
                // console.log('DAATARW', this.faxData);
            },
            getTableCategoriesData () {
                this.callStackForCategoriesTable();
            },
        },
        async fetch ({ store, params }) {
            const faxID = _.toNumber(_.get(params, 'faxID') || Cookies.get('faxID'));
            const paramsID = _.toNumber(_.get(params, 'faxID'));
            const faxDataID = _.toNumber(_.get(_.head(store.getters['fax/getFaxData']), 'fax_id'));
            const tableCategoriesDataID = _.toNumber(_.get(_.head(store.getters['fax/getTableCategoriesData']), 'fax_id'));

            try {
                if (_.isEmpty(store.getters['fax/getFaxData']) || faxDataID !== paramsID) {
                    // Запрос данных по факсу
                    const { data: faxInfoData } = await axios.post('faxes/faxData', { faxID });
                    const { groupedData = [] } = faxInfoData;
                    // console.log('Запрос данных по факсу', groupedData);

                    store.dispatch('fax/setGroupedData', groupedData);
                }

                if (_.isEmpty(store.getters['fax/getTableCategoriesData']) || tableCategoriesDataID !== paramsID) {
                    // Запрос данных категорий по факсу
                    const { data: faxCategoriesData } = await axios.post('faxes/categoriesData', { faxID });
                    // console.log('faxCategoriesData', faxCategoriesData);
                    const { tableCategoriesData = [] } = faxCategoriesData;

                    store.dispatch('fax/setTableCategoriesData', tableCategoriesData);
                }
            } catch (e) {
                console.error(`Произошла ошибка при выполнении стека запросов данных по факсу - ${e}`);
            } finally {
                console.log('Completed requests for get faxData and faxCategories.');
            }
        },
        created () {
            // console.log('CREATED');
            this.setFaxData();
            this.calcSumClientInFaxData(this.faxData);
            this.callStackForCategoriesTable();
            this.setCookies(this.$route.params);
            this.setSelectedColumn(this.$_mainTableHeaders);
            this.setChangeValues(this.faxData);
        },
        methods: {
            setChangeValues (data) {
                // console.log('DDT', data);
                _.forEach(data, elem => {
                    _.forEach(_.get(elem, 'clientItemsArray'), item => _.set(item, 'changeValues', {
                        for_kg: false,
                        for_place: false,
                    }));
                });
            },
            key () {
                return 58;
            },
            startCounter () {
                // const counterUndo = setInterval(() => {
                //     this.counter = this.counter - 1;
                //     console.log('counterUndo', this.counter);
                // }, 1000);
                //
                // setTimeout(function () {
                //     clearInterval(counterUndo);
                // }, 6000);
                // this.counter = 6;
            },
            setFaxData () {
                this.faxData = _.cloneDeep(this.getFaxData);
            },
            callStackForCategoriesTable () {
                this.tableCategoriesItems = [];
                this.groupCategoriesDataFromFaxData(this.faxData, this.tableCategoriesItems);
                this.setGroupCategoriesDataFromFaxDataForKg(this.tableCategoriesItems);
                this.setItemSumInCategoriesTable(this.tableCategoriesItems);
                this.calcTableCategoriesSum(this.tableCategoriesItems);
                this.calcTotalFaxData(this.faxData);
                this.tableCategoriesItems.push(this.$_totalCategoriesData, this.$_totalFaxData, this.differenceTotalSum);
                this.setDifferenceSum(this.differenceTotalSum, this.$_totalCategoriesData, this.$_totalFaxData);
            },
            mouse (event) {
                console.log('CN', event);
            },

            async updateFaxData () {
                console.log('sendData_UP', this.sendDataToUpdate);
                try {
                    const { data } = await axios.post('faxes/updateData', this.sendDataToUpdate);

                    const { status = false, groupedData = [] } = data;

                    if (status) {
                        this.updateTableData = false;
                        this.$store.dispatch('fax/setGroupedData', groupedData);

                        this.$snotify.success('Данные факса успешно сохранены.', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    }
                } catch (e) {
                    console.error(`Ошибка при обновлении данных факса - ${e}`);
                } finally {
                    console.log('Request completed for update fax data.');
                }
            },

            setSelectedColumn (array) {
                this.selectedColumn = _.map(array, obj => obj.text);
            },

            sendDataToExport (data, mainTableHeaders, selectedColumns) {
                const cloneData = _.cloneDeep(data);

                const mainTableHeadersValues = _.reduce(mainTableHeaders, (result, item) => {
                    if (_.includes(selectedColumns, _.get(item, 'text'))) {
                        result.push(item.value);
                    }
                    return result;
                }, []);

                _.forEach(cloneData, (item) => {
                    _.forIn(item, (value, key) => {
                        if (!_.includes(mainTableHeadersValues, key) && key !== 'brand' && key !== 'category_id') {
                            delete item[key];
                        }
                    });
                });
                // _.forEach(cloneData, item => delete item.clientItemsArray);
                console.log('cloneData', cloneData);
                console.log('mainTableHeadersValues', mainTableHeadersValues);

                return cloneData;
            },

            headersForExport (mainTableHeaders, selectedColumns) {
                return _.reduce(mainTableHeaders, (result, item) => {
                    const itemText = _.get(item, 'text');
                    if (_.includes(selectedColumns, itemText)) {
                        result.push(itemText);
                    }
                    return result;
                }, []);
            },

            sortData (data, field) {
                return data.sort((a, b) => a[field] - b[field]);
            },

            selectMenuItem (item) {
                switch (item.id) {
                    // Скачивание файла оригинала факса
                    case 0:
                        try {
                            axios({
                                url: 'faxes/load',
                                method: 'POST',
                                responseType: 'blob', // important
                                data: {
                                    fax_id: Cookies.get('faxID'),
                                },
                            }).then((response) => {
                                if (!window.navigator.msSaveOrOpenBlob) {
                                    // BLOB NAVIGATOR
                                    const url = window.URL.createObjectURL(new Blob([response.data]));
                                    const link = document.createElement('a');
                                    link.href = url;
                                    link.setAttribute('download', Cookies.get('faxNameWithExt'));
                                    document.body.appendChild(link);
                                    link.click();
                                } else {
                                    // BLOB FOR EXPLORER 11
                                    window.navigator.msSaveOrOpenBlob(new Blob([response.data]), Cookies.get('faxNameWithExt'));
                                }
                            });
                        } catch (e) {
                            console.error(`Ошибка при скачивании оригинала факса - ${e}`);
                        } finally {
                            console.log('Request completed for download original fax file');
                        }

                        break;
                    case 1:
                        // console.log('H', this.headersForExport(this.$_mainTableHeaders, this.selectedColumn));
                        console.log('FFX', this.headersForExport(this.$_mainTableHeaders, this.selectedColumn));
                        console.log('tableCategoriesItems', this.tableCategoriesItems);
                        if (_.isEmpty(this.selectedColumn)) {
                            this.$snotify.warning('Выберите столбцы для експорта', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                            return false;
                        }
                        axios({
                            url: 'fax/download',
                            method: 'POST',
                            responseType: 'blob', // important
                            data: {
                                faxData: this.sendDataToExport(this.sortData(this.faxData, 'name'), this.$_mainTableHeaders, this.selectedColumn),
                                headers: this.headersForExport(this.$_mainTableHeaders, this.selectedColumn),
                                faxID: Cookies.get('faxID'),
                                categories: this.tableCategoriesItems,
                            },
                        }).then((response) => {
                            if (!window.navigator.msSaveOrOpenBlob) {
                                // BLOB NAVIGATOR
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;
                                link.setAttribute('download', Cookies.get('faxNameWithExt'));
                                document.body.appendChild(link);
                                link.click();
                            } else {
                                // BLOB FOR EXPLORER 11
                                window.navigator.msSaveOrOpenBlob(new Blob([response.data]), Cookies.get('faxNameWithExt'));
                            }
                        });
                        break;
                    case 2:
                        this.dialogSelectedColumn = true;
                        break;
                    case 3:
                        this.sendingMessagesData(this.faxData);
                        break;
                    default:
                        console.error(`Обьект выбора меню - ${item}`);
                }
            },

            openCloseExpandedPanel (val) {
                _.set(val, 'expanded', !val.expanded);
                _.set(val, 'selected', !val.selected);
            },

            // ОТПРАВКА ДАННЫХ НА РАССЫЛКУ СООБЩЕНИЙ
            sendingMessagesData (arr) {
                // console.log('ARR', arr);
                const message = this.$store.getters['sending/message'];
                const data = _.map(arr, (item) => {
                    const obj = {};
                    const { name, sum, client_id } = item;
                    obj.name = name;
                    obj.sum = sum;
                    obj.message = message;
                    obj.client_id = client_id;
                    return obj;
                });

                // console.log('RS_AX', func(arr));
                this.$router.push({ name: 'home-sending', params: { sendingData: data } });
            },

            setCookies (params) {
                const { faxID, faxName, fileExt } = params;

                if (faxID) {
                    Cookies.set('faxID', faxID, { expires: 1 });
                    Cookies.set('faxName', faxName, { expires: 1 });
                    Cookies.set('faxNameWithExt', `${faxName}.${fileExt}`, { expires: 1 });
                }
            },

            calcSumClientInFaxData (data) {
                if (_.isArray(data)) {
                    _.forEach(data, (obj) => {
                        const sum = obj.for_kg * obj.kg + obj.for_place * obj.place;
                        _.set(obj, 'sum', _.round(sum));
                    });
                }
            },

            async saveDataCategoriesTable () {
                // Удаление последнего обькта с итоговыми данними
                const sendData = _.filter(this.tableCategoriesItems, item => !item.totalDataClass);

                try {
                    const { data } = await axios.post('faxes/updateCategoriesData', sendData);
                    const { status = false, categoriesData = [] } = data;

                    if (status) {
                        this.needSaveTableCategoriesData = false;
                        // console.log('categoriesData', categoriesData);
                        this.$store.dispatch('fax/setTableCategoriesData', categoriesData);

                        this.$snotify.success('Данные по категориям факса сохранены.', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    }
                } catch (e) {
                    console.error(`Ошибка при обновлении данных таблицы категорий факса - ${e}`);
                } finally {
                    console.log('A request has been made to update the fax category table data.');
                }
            },
            async openClientsNamesEditDialog () {
                if (_.isEmpty(this.$store.getters['fax/getClientsNames'])) {
                    try {
                        // Запрос данных по клиентам
                        const { data: clientsData } = await axios.get('clients/clientsNames');
                        const { clientsNames = [] } = clientsData;
                        // console.log('clientsData', clientsNames);
                        this.$store.dispatch('fax/setClientsNames', clientsNames);
                    } catch (e) {
                        console.error(`Произошла ошибка при запросе данных о клиентах - ${e}`);
                    } finally {
                        this.selectLoading = false;
                        console.log('Completed request for get clients names.');
                    }
                }
            },
            async openCategoriesNamesEditDialog () {
                if (_.isEmpty(this.$store.getters['fax/getCategoriesNames'])) {
                    try {
                        // Запрос данных по категория
                        const { data: categoriesData } = await axios.get('faxes/categoriesNames');
                        const { categoriesNames = [] } = categoriesData;

                        this.$store.dispatch('fax/setCategoriesNames', categoriesNames);
                    } catch (e) {
                        console.error(`Произошла ошибка при запросе данных о категория факса - ${e}`);
                    } finally {
                        this.selectLoading = false;
                        console.log('Completed request for get fax categories.');
                    }
                }
            },
            changeItemInFaxDataTeble (item) {
                console.log('item', item);
                this.updateTableData = true;
                this.addItemToSendDataArrayFromFaxDataTable(item);

                this.snack = true;
                this.snackColor = 'success';
                this.snackText = 'Сохранено';
            },
            saveInCategoriesTable () {
                this.needSaveTableCategoriesData = true;
                this.snack = true;
                this.snackColor = 'success';
                this.snackText = 'Сохранено';
            },
            async saveCommonPriceForClient (item, prop, price) {
                const elms = _.get(item, 'clientItemsArray');
                _.forEach(elms, (elem) => {
                    _.set(elem, prop, price);
                    this.changeItemInFaxDataTeble(elem);
                });
                // console.log('this.faxData', this.faxData);
                console.log('client_id', elms);
                console.log('item', item);
            },
            cancel () {
                this.startCounter();
                this.snack = true;
                this.snackColor = 'error';
                this.snackText = 'Отменено';
            },
            cancelInCategoriesTable () {

            },
            close () {
                console.log('Dialog closed');
            },
            closeInCategoriesTable () {
            },
            addItemToSendDataArrayFromFaxDataTable (item) {
                const newItem = _.clone(item);
                delete newItem.clientItemsArray;
                const itemIndex = _.findIndex(this.sendDataToUpdate, { id: newItem.id });

                if (itemIndex !== -1) {
                    this.sendDataToUpdate.splice(itemIndex, 1, newItem);
                } else {
                    this.sendDataToUpdate.push(newItem);
                }
            },
            // НАЧАЛО СТЕК ВЫЗОВОВ ФУНКЦИЙ ДЛЯ ФОРМИРОВАНИЯ ТАБЛИЦЫ КАТЕГОРИЙ
            groupCategoriesDataFromFaxData (mainArray, array) {
                _.forEach(mainArray, (obj) => {
                    const findedObj = _.find(array, { category_name: obj.category_name });

                    if (findedObj) {
                        findedObj.place += obj.place;
                        findedObj.kg += obj.kg;
                    } else {
                        array.push({
                            category_name: obj.category_name,
                            category_id: obj.category_id,
                            fax_id: obj.fax_id,
                            place: obj.place,
                            kg: obj.kg,
                            for_kg: 0,
                            sum: 0,
                        });
                    }
                });
            },

            setGroupCategoriesDataFromFaxDataForKg (array) {
                _.forEach(array, (item) => {
                    const findCategoryObj = _.find(this.getTableCategoriesData, { category_id: item.category_id });
                    if (findCategoryObj) {
                        _.set(item, 'for_kg', _.get(findCategoryObj, 'category_price'));
                    }
                });
            },

            setItemSumInCategoriesTable (array) {
                _.forEach(array, (item) => {
                    const sum = item.kg * item.for_kg;
                    _.set(item, 'sum', _.floor(sum, 2));
                });
            },

            calcTableCategoriesSum (array) {
                const data = {
                    kg: 0,
                    place: 0,
                    sum: 0,
                };

                _.forEach(array, (item) => {
                    if (!item.totalDataClass) {
                        data.kg += item.kg;
                        data.place += item.place;
                        data.sum += _.round(item.sum);
                    }
                });

                _.assign(this.$_totalCategoriesData, data);
            },

            calcTotalFaxData (array) {
                this.$_totalFaxData = _.assign({}, this.$_totalFaxDataDefaultValues);

                _.forEach(array, (item) => {
                    this.$_totalFaxData.kg += item.kg;
                    this.$_totalFaxData.place += item.place;
                    this.$_totalFaxData.sum += item.sum;
                });
            },

            setDifferenceSum (diffObj, categoriesData, faxData) {
                diffObj.sum = faxData.sum - categoriesData.sum;
            },
            // КОНЕЦ СТЕК ВЫЗОВОВ ФУНКЦИЙ ДЛЯ ФОРМИРОВАНИЯ ТАБЛИЦЫ КАТЕГОРИЙ
        },
    };
</script>

<style lang="scss">

    .table__tr-bold_text {
        font-weight: bold !important;

        & > td {
            font-weight: bold !important;
        }
    }

    .table__tr-bg_color {
        background-color: #dde0e2;
    }

    .table__td-color-text {
        color: #ff5a4d;
        font-weight: bold;
    }

    .table__td-badge .v-badge__badge {
        width: auto;
        font-weight: normal;
        border-radius: 12px;
        padding: 0 5px;
        right: -53px;
    }

    .table__tr-active_color {
        background-color: #EEEEEE;
    }

    div.table__tr-border_color {
        tr {
            border-color: green !important;
        }
    }

    div.table__thead-bg_color {
        thead {
            background-color: #c1d6f5 !important;
        }
    }

    .table__td-hide {
        display: none;
    }

    .table_categories {
        margin-top: 30px;
        max-width: 60%;
    }

    .table__td_categories_table_total_data, .table__td_fax_table_total_data {
        background-color: #FFFF00;
    }

    .table__td_fax_difference_table_total_data_bg {
        background-color: #5ccd61;
    }

    /*tr > td {*/
    /*border: 1px dashed lightgrey;*/
    /*}*/
    tr.table__tr_show_hide_tr_control_panel {
        position: relative;
    }

    .table__tr_show_hide_tr_control_panel {
        .table_tr_control_panel {
            display: none;
            position: absolute;
            margin-top: 10px;
            right: 5%;

            /*.control_panel__button_wraper {*/
            /*display: flex;*/
            /*justify-content: space-between;*/
            /*}*/
        }

        &:hover {
            .table_tr_control_panel {
                display: block;
            }
        }
    }

    .table__tr_show_hide_tr_control_panel {
        position: relative;

        .fax_table_control_panel {
            position: absolute;
            margin-top: 10px;
            right: 10%;
            display: none;
        }

        &:hover {
            .fax_table_control_panel {
                display: block;
            }
        }
    }

</style>
