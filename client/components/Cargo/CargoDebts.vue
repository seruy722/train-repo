<template>
    <div data-component-name="CargoDebts">
        <v-card>
            <v-card-actions>
                <v-btn class="white--text main_color-bg" @click="addEmptyEntry">
                    <span>Долг</span>
                    <v-icon>add</v-icon>
                </v-btn>
            </v-card-actions>

            <v-container grid-list-sm class="pa-4">
                <v-layout row wrap class="borderForEntry" v-for="(item, index) in cargoDebtsList" :key="index">

                    <!--ДАТА-->
                    <v-flex xs12 sm2 md2>
                        <date-picker :value.sync="item.date"></date-picker>
                    </v-flex>

                    <v-flex xs12 sm3 md3>
                        <v-text-field
                            v-model="item.client"
                            :error-messages="checkError('client')"
                            prepend-icon="person"
                            label="Клиент"
                            autofocus
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm2 md2>
                        <v-text-field
                            v-model="item.sum"
                            :error-messages="checkError('sum')"
                            prepend-icon="monetization_on"
                            label="Сумма"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm2 md2>
                        <v-text-field
                            v-model="item.place"
                            :error-messages="checkError('place')"
                            prepend-icon="phone"
                            label="Мест"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm2 md2>
                        <v-text-field
                            v-model="item.kg"
                            :error-messages="checkError('kg')"
                            prepend-icon="notes"
                            label="Вес"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm4 md4>
                        <v-text-field
                            v-model="item.fax"
                            :error-messages="checkError('fax')"
                            prepend-icon="notes"
                            label="Факс"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm5 md5>
                        <v-text-field
                            v-model="item.notation"
                            :error-messages="checkError('notation')"
                            prepend-icon="notes"
                            label="Примечание"
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
    </div>
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
                loadOnBtn: false, // Оверлей для кнопки
                selected: [],
                dataDebts: [
                    {
                        type: 'ДОЛГ',
                        date: null,
                        sum: 0,
                        sale: 0,
                        client: null,
                        place: 0,
                        kg: 0,
                        fax: null,
                        notation: null,
                    }
                ],
                defaultItem: {
                    type: 'ДОЛГ',
                    date: null,
                    sum: 0,
                    sale: 0,
                    client: null,
                    place: 0,
                    kg: 0,
                    fax: null,
                    notation: null,
                },
            }
        },
        computed: {
            cargoDebtsList () {
                return this.dataDebts;
            }
        },
        methods: {
            clearAndCloseComponent(){
                this.$store.commit('controlPanel/SET_OPENEDCOMPONENT', false);
                this.dataDebts = [];
                this.addEmptyEntry();
            },
            deleteEntry(elem){
               const elemIndex = _.indexOf(this.dataDebts, elem);
               this.dataDebts.splice(elemIndex, 1);
            },
            addEmptyEntry(){
                this.dataDebts.push(_.assign({}, this.defaultItem));
            },
            changeLoadBtn () {
                this.loadOnBtn = !this.loadOnBtn;
            },
            async save () {
                console.log('SAVE', this.dataDebts);
                this.changeLoadBtn();
                this.changeErrors({});

                // Очищаем обьект от ложных данных
                const clearDataProfit = [];
                _.forEach(this.dataDebts, (item) => {
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
                this.changeLoadBtn();
                return;

                await this.saveCargoDebtsToServer(clearDataProfit).then((response) => {
                    const {status} = response.data;

                    if (status) {
                        this.changeLoadBtn();
                        let { cargoDebts } = response.data;
                        // Форматируем дату
                        cargoDebts = formatDate(cargoDebts, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');
                        _.forEach(cargoDebts, (item)=>{
                            this.$store.commit('cargo/ADD_ITEM', item);

                            this.$snotify.success('Запись успешно добавлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true
                            });
                        });
                    }
                }).catch((errors) => {
                    this.changeLoadBtn();
                    this.changeErrors(errors.response.data.errors);
                });
            },
            async saveCargoDebtsToServer (item) {
                return await axios.post('cargo/saveUpdate', item);
            },
            async deleteCargoDebtsFromServer (id) {
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
