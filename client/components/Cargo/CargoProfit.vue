<template>
    <v-card>
        <v-card-actions>
            <v-btn class="white--text main_color-bg" @click="addEmptyEntry">
                <span>Доход</span>
                <v-icon>add</v-icon>
            </v-btn>

            <v-spacer></v-spacer>

            <v-flex xs12 sm2 md2>
                <date-picker :value.sync="dateAdd"></date-picker>
            </v-flex>

            <v-flex xs12 sm3 md3>
                <v-combobox
                    v-model="currentClient"
                    :items="clients"
                    prepend-icon="people"
                    label="Клиент"
                    item-text="name"
                    item-value="id"
                    return-object
                    disabled
                ></v-combobox>
            </v-flex>
        </v-card-actions>

        <v-container grid-list-sm class="pa-4">
            <v-layout row wrap v-for="(item, index) in getDataProfit" :key="index" class="borderForEntry">

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.sum"
                        :error-messages="checkError('sum')"
                        prepend-icon="monetization_on"
                        autofocus
                        type="number"
                        label="Сумма"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.sale"
                        :error-messages="checkError('sale')"
                        prepend-icon="money_off"
                        type="number"
                        label="Скидка"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm3 md3>
                    <v-text-field
                        v-model="item.notation"
                        :error-messages="checkError('notation')"
                        prepend-icon="notes"
                        label="Примечание"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm1 md1>
                    <v-btn
                        fab
                        small
                        dark
                        color="error"
                        @click="deleteEntry(item)"
                    >
                        <v-icon dark>delete</v-icon>
                    </v-btn>
                </v-flex>

            </v-layout>
        </v-container>

        <v-card-actions v-if="dataProfit.length">

            <v-spacer></v-spacer>

            <v-btn
                :disabled="loadOnBtn"
                :loading="loadOnBtn"
                fab
                small
                dark
                color="primary"
                @click="save"
            >
                <v-icon dark>save</v-icon>
            </v-btn>

            <v-btn
                fab
                small
                dark
                color="error"
                @click="clearAndCloseComponent"
            >
                <v-icon dark>clear</v-icon>
            </v-btn>

            <v-spacer></v-spacer>

        </v-card-actions>
    </v-card>
</template>
<script>
    import axios from 'axios';
    import DatePicker from '~/components/Pickers/DatePicker.vue';
    import { formatDate } from '~/utils';
    import checkErrorMixin from '~/mixins/checkError';
    import { mapGetters } from 'vuex';

    export default {
        components: {
            DatePicker,
        },
        middleware: 'auth',
        mixins: [checkErrorMixin],
        data () {
            return {
                loadOnBtn: false, // Оверлей для кнопки
                dataProfit: [],
                defaultItem: {
                    type: 'ОПЛАТА',
                    sum: null,
                    sale: null,
                    notation: null,
                },
            };
        },
        computed: {
            ...mapGetters({
                clients: 'cargo/clientsNames',
                currentClient: 'cargo/getCurrentClient',
                dateForAddEntry: 'cargo/getDateForAddEntry',

            }),
            getDataProfit () {
                return this.dataProfit;
            },
            dateAdd: {
                get:function () {
                    return this.dateForAddEntry;
                },
                set:function (val) {
                    this.$store.commit('cargo/SET_DATEFORADDENTRY', val);
                },
            },
        },
        created () {
            this.addEmptyEntry();
        },
        methods: {
            setDefaultValues () {
                this.defaultItem.client = this.currentClient;
                this.defaultItem.date = this.$store.getters['cargo/getcurrentDate'];
            },
            clearAndCloseComponent () {
                this.$store.commit('controlPanel/SET_OPENEDCOMPONENT', false);
                this.dataProfit = [];
                this.addEmptyEntry();
            },
            deleteEntry (elem) {
                const elemIndex = _.indexOf(this.dataProfit, elem);
                this.dataProfit.splice(elemIndex, 1);

                if (_.isEmpty(this.dataDebts)) {
                    this.addEmptyEntry();
                }
            },
            addValuesToEntries (props) {
                return _.reduce(props, (result, item) => {
                    result.push(_.assign(item, {
                        client: this.currentClient,
                        date: this.dateForAddEntry,
                    }));
                    return result;
                }, []);
            },
            addEmptyEntry () {
                this.setDefaultValues();
                this.dataProfit.push(_.assign({}, this.defaultItem));
            },
            changeLoadBtn () {
                this.loadOnBtn = !this.loadOnBtn;
            },
            async save () {
                const sendData = this.addValuesToEntries(this.dataProfit);
                console.log('sendData', sendData);
                return;
                this.changeLoadBtn();
                this.changeErrors({});

                // Очищаем обьект от ложных данных
                const clearDataProfit = [];
                _.forEach(this.dataProfit, (item) => {
                    const obj = _.pickBy(item, _.identity);
                    if (!_.isEmpty(obj)) {
                        clearDataProfit.push(obj);
                    }
                });

                console.log(clearDataProfit);

                await this.saveCargoProfitToServer(clearDataProfit).then((response) => {
                    const { status } = response.data;

                    if (status) {
                        this.changeLoadBtn();
                        let { cargoEntry } = response.data;
                        // Форматируем дату
                        cargoEntry = formatDate(cargoEntry, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');
                        _.forEach(cargoEntry, (item) => {
                            this.$store.commit('cargo/ADD_ITEM', item);

                            this.$snotify.success('Запись успешно добавлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                        });

                        // if (type === 'update') {
                        //     const data = {item, index: this.editedIndex};
                        //     this.updateItemInStorage(data);
                        //     this.$snotify.success('Запись успешно обновлена', {
                        //         timeout: 3000,
                        //         showProgressBar: true,
                        //         closeOnClick: true,
                        //         pauseOnHover: true
                        //     });
                        // } else if (type === 'save') {
                        //     this.addItemToStorage(item);
                        //     this.$snotify.success('Запись успешно добавлена!', {
                        //         timeout: 3000,
                        //         showProgressBar: true,
                        //         closeOnClick: true,
                        //         pauseOnHover: true
                        //     });
                        // }
                    }
                }).catch((errors) => {
                    this.changeLoadBtn();
                    this.changeErrors(errors.response.data.errors);
                });
            },
            async saveCargoProfitToServer (item) {
                return await axios.post('cargo/saveProfit', item);
            },
            async deleteItemFromServer (id) {
                return await axios.post('blacklist/delete', { id });
            },
        }
    }
</script>

<style scoped>
    .borderForEntry {
        border-bottom: 1px solid #0080FF;
    }
</style>
