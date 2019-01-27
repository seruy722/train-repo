<template>
    <div data-component-name="CargoDebts">
        <v-card>
            <v-card-actions>
                <v-btn class="white--text main_color-bg" @click="addEmptyEntry">
                    <span>Долг</span>
                    <v-icon>add</v-icon>
                </v-btn>

                <v-spacer></v-spacer>

                <v-flex xs12 sm2 md2>
                    <date-picker :value.sync="dateAdd"></date-picker>
                </v-flex>

                <v-flex xs12 sm3 md3>
                    <v-text-field
                        v-model="fax"
                        prepend-icon="notes"
                        label="Факс"
                        type="text"
                    ></v-text-field>
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

                <!--<v-flex xs12 sm3 md3>-->
                    <!--<v-text-field-->
                        <!--v-model="currentClient"-->
                        <!--prepend-icon="people"-->
                        <!--label="Клиент"-->
                        <!--item-text="name"-->
                        <!--item-value="id"-->
                        <!--return-object-->
                        <!--disabled-->
                    <!--&gt;</v-text-field>-->
                <!--</v-flex>-->

            </v-card-actions>

            <v-container grid-list-sm class="pa-4">
                <v-layout row wrap class="borderForEntry" v-for="(item, index) in cargoDebtsList" :key="index">

                    <!--<v-flex xs12 sm2 md2>-->
                        <!--<v-text-field-->
                            <!--v-model="item.sum"-->
                            <!--:error-messages="checkError(`${index}.sum`)"-->
                            <!--prepend-icon="monetization_on"-->
                            <!--type="number"-->
                            <!--label="Сумма"-->
                            <!--autofocus-->
                            <!--@focus="$event.target.select()"-->
                        <!--&gt;</v-text-field>-->
                    <!--</v-flex>-->

                    <v-flex xs12 sm2 md2>
                        <v-text-field
                            v-model="item.place"
                            :error-messages="checkError(`${index}.place`)"
                            prepend-icon="phone"
                            label="Мест"
                            type="number"
                            @focus="$event.target.select()"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm2 md2>
                        <v-text-field
                            v-model="item.kg"
                            :error-messages="checkError(`${index}.kg`)"
                            prepend-icon="notes"
                            label="Вес"
                            type="number"
                            @focus="$event.target.select()"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm3 md3>
                        <v-text-field
                            v-model="item.notation"
                            :error-messages="checkError(`${index}.notation`)"
                            prepend-icon="notes"
                            label="Примечание"
                            type="text"
                        ></v-text-field>
                    </v-flex>

                    <v-flex xs12 sm1 md1>
                        <v-tooltip top>
                            <v-btn
                                slot="activator"
                                fab
                                small
                                dark
                                color="error"
                                @click="deleteEntry(item)"
                            >
                                <v-icon dark>delete</v-icon>
                            </v-btn>
                            <span>Удалить</span>
                        </v-tooltip>
                    </v-flex>

                </v-layout>
            </v-container>

            <v-card-actions>

                <v-spacer></v-spacer>
                <v-tooltip top>
                    <v-btn
                        slot="activator"
                        :loading="loadOnBtn"
                        fab
                        small
                        dark
                        color="primary"
                        @click="save"
                    >
                        <v-icon dark>save</v-icon>
                    </v-btn>
                    <span>Сохранить и закрыть</span>
                </v-tooltip>

                <v-tooltip top>
                    <v-btn
                        slot="activator"
                        fab
                        small
                        dark
                        color="error"
                        @click="clearAndCloseComponent"
                    >
                        <v-icon dark>clear</v-icon>
                    </v-btn>

                    <span>Отменить и закрыть</span>
                </v-tooltip>

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
                selected: [],
                dataDebts: [],
                fax: null,
                defaultItem: {
                    type: 'ДОЛГ',
                    sum: 0,
                    place: 0,
                    kg: 0,
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
            cargoDebtsList () {
                return this.dataDebts;
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
        watch: {
            currentClient (val) {
                if (val.name === 'Все') {
                    this.clearAndCloseComponent();
                }
            },
        },
        created () {
            this.addEmptyEntry();
            console.log('CURCLIENT', this.currentClient);
        },
        methods: {
            clearAndCloseComponent () {
                this.$store.commit('controlPanel/SET_OPENEDCOMPONENT', false);
                this.dataDebts = [];
                this.addEmptyEntry();
            },
            deleteEntry (elem) {
                const elemIndex = _.indexOf(this.dataDebts, elem);
                this.dataDebts.splice(elemIndex, 1);

                if (_.isEmpty(this.dataDebts)) {
                    this.addEmptyEntry();
                }
            },
            addEmptyEntry () {
                this.dataDebts.push(_.assign({}, this.defaultItem));
            },
            addValuesToEntries (props) {
                return _.reduce(props, (result, item) => {
                    result.push(_.assign(item, {
                        client: this.currentClient,
                        date: this.dateForAddEntry,
                        fax: this.fax,
                    }));
                    return result;
                }, []);
            },
            changeLoadBtn () {
                this.loadOnBtn = !this.loadOnBtn;
            },
            async save () {
                const sendData = this.addValuesToEntries(this.dataDebts);
                console.log('SAVE',sendData);
                return;
                console.log('SAVE', this.dataDebts);
                this.changeLoadBtn();
                this.changeErrors({});

                await this.saveCargoDebtsToServer(this.dataDebts).then((response) => {
                    const { status } = response.data;

                    if (status) {
                        this.changeLoadBtn();
                        let { cargoEntry } = response.data;
                        // Форматируем дату
                        cargoEntry = formatDate(cargoEntry, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');
                        console.log('cargoDebts', cargoEntry);
                        _.forEach(cargoEntry, (item) => {
                            this.$store.commit('cargo/ADD_ITEM', item);

                            this.$snotify.success('Запись успешно добавлена!', {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                        });
                    }
                }).catch((errors) => {
                    this.changeLoadBtn();
                    this.changeErrors(errors.response.data.errors);
                });
            },
            async saveCargoDebtsToServer (item) {
                return axios.post('cargo/saveUpdate', item);
            },
            async deleteCargoDebtsFromServer (id) {
                return axios.post('blacklist/delete', { id });
            },
        },
    };
</script>

<style scoped>
    .borderForEntry {
        border-bottom: 1px solid #0080FF;
    }
</style>
