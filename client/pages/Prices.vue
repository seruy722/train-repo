<template>
    <div
        data-vue-component-name="Prices"
        class="main"
    >
        <Search :value.sync="search" class="mb-3"/>
        <v-expansion-panel>
            <v-expansion-panel-content
                v-for="(item, i) in items"
                :key="i"
                ripple
            >
                <template v-slot:actions>
                    <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-icon
                                v-on="on"
                                color="primary"
                                @click.stop="checkAddItem(item)"
                                class="mr-5"
                            >
                                add_circle
                            </v-icon>
                        </template>
                        <span>Добавить запись</span>
                    </v-tooltip>
                    <v-icon color="teal">done</v-icon>
                </template>
                <template v-slot:header>
                    <div>
                        {{ item.name }}
                    </div>
                </template>
                <v-data-table
                    :headers="$_headers"
                    :items="item.children"
                    class="elevation-1"
                    hide-actions
                >
                    <template v-slot:headers="props">
                        <tr class="table_thead_tr">
                            <th
                                v-for="header in props.headers"
                                :key="header.text"
                                class="text-xs-center"
                            >
                                {{ header.text }}
                            </th>
                        </tr>
                    </template>
                    <template v-slot:items="props">
                        <td class="text-xs-center">
                            <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                    <v-btn v-on="on" flat icon color="red lighten-2">
                                        <v-icon
                                            color="teal"
                                            @click="editItem(props.item)"
                                        >
                                            edit
                                        </v-icon>
                                    </v-btn>
                                </template>
                                <span>Редактировать</span>
                            </v-tooltip>

                            <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                    <v-btn v-on="on" flat icon color="red lighten-2">
                                        <v-icon
                                            color="red"
                                            @click="deleteItem(props.item)"
                                        >
                                            delete
                                        </v-icon>
                                    </v-btn>
                                </template>
                                <span>Удалить</span>
                            </v-tooltip>
                        </td>
                        <td class="text-xs-center">{{ props.item.category_id | categoryName(getCategories) }}</td>
                        <td class="text-xs-center">{{ props.item.for_kg }}</td>
                        <td class="text-xs-center">{{ props.item.for_place }}</td>
                        <td class="text-xs-center">{{ props.item.user_id }}</td>
                        <td class="text-xs-center">{{ props.item.updated_at | formatDateWithTime }}</td>
                    </template>
                </v-data-table>
            </v-expansion-panel-content>
        </v-expansion-panel>
        <!--<DialogEditPrice :open="openDialog" :item="currentItem"/>-->

        <!--ДИАЛОГ РЕДАКТИРОВАНИЯ-->
        <template>
            <v-layout row justify-center>
                <v-dialog v-model="dialog" persistent max-width="600px">
                    <v-card>
                        <v-card-title class="headline grey lighten-2">
                            <span class="headline">{{ dialogTitle }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container grid-list-md>
                                <v-layout wrap>
                                    <v-flex xs12 sm6 md6>
                                        <v-autocomplete
                                            v-model.number="currentItem.category_id"
                                            :items="getCategories"
                                            :error-messages="checkError('category_id')"
                                            item-text="category_name"
                                            item-value="id"
                                            label="Категория"
                                        ></v-autocomplete>
                                    </v-flex>
                                    <v-flex xs12 sm3 md3>
                                        <v-text-field
                                            v-model.number="currentItem.for_kg"
                                            :error-messages="checkError('for_kg')"
                                            type="number"
                                            label="За кг"
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs12 sm3 md3>
                                        <v-text-field
                                            v-model.number="currentItem.for_place"
                                            :error-messages="checkError('for_place')"
                                            type="number"
                                            label="За место"
                                        ></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                            <!--<small>*indicates required field</small>-->
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="red darken-1" outline flat @click="closeDialog">Закрыть</v-btn>
                            <v-btn color="blue darken-1" outline flat @click="saveUpdate(currentItem)">Сохранить
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-layout>
        </template>

        <!--<Overlay :overlay="overlay"/>-->
    </div>
</template>

<script>
    import axios from 'axios';
    import { universalSort } from '~/utils';
    import { mapGetters } from 'vuex';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        name: 'Prices',
        filters: {
            categoryName (val, arrCategories) {
                const category = _.find(arrCategories, { id: val });
                return _.get(category, 'category_name');
            },
        },
        components: {
            Search: () => import('~/components/Search'),
            // Overlay: () => import('~/components/Overlay'),
            // DialogEditPrice: () => import('~/components/Dialogs/DialogEditPrice'),
        },
        mixins: [checkErrorMixin],
        data () {
            this.$_headers = [
                { text: 'Управление', align: 'center', value: 'for_kg', sortable: false },
                { text: 'Категория', align: 'center', sortable: false, value: 'for_place' },
                { text: 'За кг', align: 'center', sortable: false, value: 'for_kg' },
                { text: 'За место', align: 'center', sortable: false, value: 'for_place' },
                { text: 'Пользователь', align: 'center', sortable: false, value: 'user_id' },
                { text: 'Дата редактирования', align: 'center', sortable: false, value: 'updated_at' },
            ];

            this.prices = [];
            this.$_duplicateCurrentItem = {};

            return {
                dialog: false,
                dialog2: false,
                dialogProgress: false,
                items: [],
                search: '',
                currentItem: {},
                overlay: false,
                dialogTitle: '',
                operation: 1,
            };
        },
        computed: {
            ...mapGetters({
                getCategories: 'fax/getCategories',
                getPrices: 'price/getPrices',
            }),
        },
        watch: {
            search (val) {
                this.overlay = true;
                this.filter(this.getPrices, val);
                this.overlay = false;
            },
        },
        async asyncData ({ store }) {
            console.log('asyncData');
            if (_.isEmpty(store.getters['price/getPrices'])) {
                console.log('ENTER');
                const { data } = await axios.get('prices/all');
                const func = ((data) => {
                    const arr = [];
                    _.forEach(data, (value, key) => {
                        const id = _.get(_.first(value), 'client_id');
                        arr.push({
                            name: key,
                            children: value,
                            client_id: id,
                        });
                    });
                    return universalSort(arr);
                });

                const res = func(data.priceData);
                // console.log('RES', res);
                store.dispatch('price/setPrices', _.cloneDeep(res));
                return { items: res };
            }
            return { items: store.getters['price/getPrices'] };
        },
        created () {
            if (_.isEmpty(this.getCategories)) {
                this.$store.dispatch('fax/getCategories');
            }

            this.$store.dispatch('settings/setPageSettings', { title: 'Цены', icon: 'attach_money' });
        },
        methods: {
            saveUpdate (item) {
                if (this.operation) {
                    this.addItem(this.currentItem);
                } else {
                    this.updatePriceData(item);
                }
            },
            closeDialog () {
                this.changeErrors({});
                this.dialog = false;
                this.restoreDefaultData(this.currentItem, this.$_duplicateCurrentItem);
            },
            restoreDefaultData (item, defaultItem) {
                _.forEach(item, (value, key, obj) => {
                    obj[key] = defaultItem[key];
                });
            },
            editItem (item) {
                this.operation = 0;
                this.currentItem = item;
                this.dialog = true;
                this.dialogTitle = `Клиент: ${item.name}`;
                // console.log('currentItem', item);
                this.$_duplicateCurrentItem = _.clone(item);
            },
            addItem (item) {
                try {
                    axios.post('price/add', item).then((response) => {
                        const { data } = response;
                        const { status = false, priceEntry = {} } = data;

                        if (status) {
                            this.changeErrors({});
                            const { name = '' } = item;
                            if (!_.isEmpty(priceEntry)) {
                                _.set(priceEntry, 'name', name);
                                this.addUpdateLocalItem(name, priceEntry);
                                this.dialog = false;

                                this.$snotify.success('Запись успешно добавлена', {
                                    timeout: 3000,
                                    showProgressBar: true,
                                    closeOnClick: true,
                                    pauseOnHover: true,
                                });
                            }
                        } else {
                            const { answer } = data;
                            this.$snotify.success(answer, {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                        }
                    });
                } catch (errors) {
                    this.changeErrors(errors.response.data.errors);
                    console.error(`Произошла ошибка при сохранении - ${errors}`);
                } finally {
                    console.log('Completed request add price');
                }

                // console.log('ITEM', this.currentItem);
            },
            addUpdateLocalItem (name, item) {
                const findItem = _.find(this.items, { name });
                if (findItem) {
                    const { children = [] } = findItem;
                    if (this.operation) {
                        console.log('ITEM_ADD', item);
                        children.push(item);
                    } else {
                        const itemIndex = _.findIndex(children, { id: item.id });
                        children.splice(itemIndex, 1, item);
                        console.log('ITEM_UPDATE', item);
                    }
                }
            },
            checkAddItem (item) {
                // console.log('CHECKITEN', item);
                this.operation = 1;
                const { client_id = 0, name = '' } = item;
                if (client_id) {
                    this.dialog = true;
                    this.dialogTitle = `Добавление записи клиенту: ${name}`;
                    // this.currentItem.name = name;
                    _.set(this.currentItem, 'name', name);
                    _.set(this.currentItem, 'client_id', client_id);
                    _.set(this.currentItem, 'category_id', 0);
                    _.set(this.currentItem, 'for_kg', 0);
                    _.set(this.currentItem, 'for_place', 0);
                } else {
                    this.$snotify.warning('Нельзя добавить запись этому клиенту', {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                    });
                }
            },
            async deleteItem (item) {
                const confirmation = confirm('Удалить запись?');
                if (confirmation) {
                    try {
                        const { id, name } = item;
                        const { data } = await axios.post('price/delete', { ID: id });
                        const { status = false } = data;
                        if (status) {
                            const findItem = _.find(this.items, { name });
                            if (findItem) {
                                // УДАЛЕНИЕ ЭЛЕМЕНТА ЛОКАЛЬНО
                                const { children = [] } = findItem;
                                const findEntryIndex = _.findIndex(children, { id });
                                if (findEntryIndex !== -1) {
                                    children.splice(findEntryIndex, 1);
                                    this.dialog = false;
                                }
                                // Если у клиента пустой массив цен - удаляем клиента
                                if (_.isEmpty(children)) {
                                    const itemIndex = _.findIndex(this.items, { name });
                                    if (itemIndex !== -1) {
                                        this.items.splice(itemIndex, 1);
                                    }
                                }

                                this.$snotify.success('Запись успешно удалена.', {
                                    timeout: 3000,
                                    showProgressBar: true,
                                    closeOnClick: true,
                                    pauseOnHover: true,
                                });
                            }
                        }
                    } catch (e) {
                        this.$snotify.error('Произошла ошибка при удалении записи.', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                        console.error(`Ошибка при удалении цены - ${e}`);
                    } finally {
                        console.log('Completed request destroy price');
                    }
                }
            },
            async updatePriceData (item) {
                if (!_.isEqual(item, this.$_duplicateCurrentItem)) {
                    try {
                        this.changeErrors({});
                        // console.log('updatePriceData', item);
                        const { data } = await axios.post('prices/update', item);
                        const { status = false, updatedEntry = [] } = data;
                        // console.log('USER', status);
                        if (status) {
                            const { name } = item;
                            this.addUpdateLocalItem(name, updatedEntry);
                            this.dialog = false;
                            this.$snotify.success(`Данные клиента - ${name} успешно сохранены`, {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                        }
                    } catch (errors) {
                        this.changeErrors(errors.response.data.errors);
                        console.error(`Ошибка при обновлении цены - ${errors}`);
                    } finally {
                        console.log('Completed request update price.');
                    }
                }
            },
            filter: _.debounce(function (data, search) {
                // console.log('SEAR');
                if (!search) {
                    this.items = _.cloneDeep(data);
                } else {
                    const searchItems = _.filter(data, item => _.includes(_.toLower(item.name), search));
                    this.items = _.cloneDeep(searchItems);
                }
            }, 300),
        },
    };
</script>

<style lang="scss" scoped>
    table.v-datatable thead {
        background-color: blue !important;
    }
</style>
