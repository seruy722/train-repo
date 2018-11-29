<template>
    <v-card>
        <v-card-actions>
            <v-btn class="white--text main_color-bg" @click="addEmptyEntry">
                <span>Доход</span>
                <v-icon>add</v-icon>
            </v-btn>
        </v-card-actions>

        <v-container grid-list-sm class="pa-4">
            <v-layout row wrap class="borderForEntry" v-for="(item, index) in getDataProfit" :key="index">

                <!--ДАТА-->
                <v-flex xs12 sm2 md2>
                    <DatePicker :value.sync="item.date"></DatePicker>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.client"
                        :error-messages="checkError('client')"
                        prepend-icon="person"
                        placeholder="Клиент"
                        autofocus
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.sum"
                        :error-messages="checkError('sum')"
                        prepend-icon="monetization_on"
                        placeholder="Сумма"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-text-field
                        v-model="item.sale"
                        :error-messages="checkError('sale')"
                        prepend-icon="money_off"
                        placeholder="Скидка"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm3 md3>
                    <v-text-field
                        v-model="item.notation"
                        :error-messages="checkError('notation')"
                        prepend-icon="notes"
                        placeholder="Примечание"
                    ></v-text-field>
                </v-flex>

                <v-flex xs12 sm1 md1>
                    <v-btn
                        v-if="index > 0"
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

        <v-card-actions>

            <v-spacer></v-spacer>

            <v-btn
                v-if="dataProfit.length"
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
    import DatePicker from '~/components/Cargo/Control/DatePicker/DatePicker';
    import { formatDate } from '~/utils';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        components: {
            DatePicker
        },
        middleware: 'auth',
        mixins: [checkErrorMixin],
        data () {
            return {
                search: '',
                loadOnBtn: false, // Оверлей для кнопки
                selected: [],
                dataProfit: [
                    {
                        type: 'ОПЛАТА',
                        client: null,
                        date: null,
                        sum: 0,
                        sale: null,
                        notation: null,
                    }
                ],
                defaultItem: {
                    type: 'ОПЛАТА',
                    client: null,
                    date: null,
                    sum: 0,
                    sale: null,
                    notation: null,
                },
            }
        },
        computed: {
            getDataProfit () {
                return this.dataProfit;
            },
        },
        methods: {
            clearAndCloseComponent () {
                this.$store.commit('controlPanel/SET_OPENEDCOMPONENT', false);
                const emptyDataPtofit = [];
                emptyDataPtofit.push(_.assign({}, this.defaultItem));
                this.dataProfit = emptyDataPtofit;
            },
            deleteEntry (elem) {
                const elemIndex = _.indexOf(this.dataProfit, elem);
                this.dataProfit.splice(elemIndex, 1);
            },
            addEmptyEntry () {
                this.dataProfit.push(_.assign({}, this.defaultItem));
            },
            changeLoadBtn () {
                this.loadOnBtn = !this.loadOnBtn;
            },
            async save () {
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

                // Проверяем массив на пустоту
                if (_.isEmpty(clearDataProfit)) {
                    this.$snotify.warning('Данные не заполнены', {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true
                    });

                    return;
                }
                console.log(clearDataProfit);

                await this.saveCargoProfitToServer(clearDataProfit).then((response) => {
                    const { status } = response.data;


                    if (status) {
                        this.changeLoadBtn();
                        let { cargoEntry } = response.data;
                        // Форматируем дату
                        cargoEntry = formatDate(cargoEntry, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');
                        _.forEach(cargoEntry, (item)=>{
                            this.$store.commit('cargo/ADD_ITEM', item);

                            this.$snotify.success('Запись успешно добавлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
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
                return await axios.post('blacklist/delete', {id});
            },
        }
    }
</script>

<style scoped>
    .borderForEntry {
        border-bottom: 1px solid #0080FF;
    }
</style>
