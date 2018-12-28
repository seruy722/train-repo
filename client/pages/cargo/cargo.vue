<template>

    <div class="main" data-component-name="Cargo">
        <v-container fluid>

            <v-toolbar>
                <v-toolbar-title class="text-xs-center title blue--text">{{ currentTable }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <search :value.sync="search"></search>
            </v-toolbar>

            <v-toolbar flat color="white" evaluation-1>
                <control-panel></control-panel>
                <v-spacer></v-spacer>

                <!--МЕНЮ ДОБАВЛЕНИЯ ЗАПИСЕЙ В КАРГО И ДОЛГИ-->
                <cargo-debts-nav @click-nav="clickNav"></cargo-debts-nav>

                <!--<v-dialog v-model="dialog" width="800px">-->

                <!--<v-card>-->
                <!--<v-card-title-->
                <!--class="grey lighten-4 py-4 title"-->
                <!--&gt;-->
                <!--<span class="headline">{{ dialogTitle }}</span>-->
                <!--</v-card-title>-->
                <!--<v-container grid-list-sm class="pa-4">-->
                <!--<v-layout row wrap>-->
                <!--<v-flex xs12 sm12 md12>-->
                <!--<v-text-field-->
                <!--v-model="editedItem.fio"-->
                <!--:error-messages="checkError('fio')"-->
                <!--prepend-icon="person"-->
                <!--autofocus-->
                <!--placeholder="ФИО"-->
                <!--&gt;</v-text-field>-->
                <!--</v-flex>-->
                <!--<v-flex-->
                <!--xs12 sm4 md4-->
                <!--&gt;-->
                <!--<v-text-field-->
                <!--v-model="editedItem.phone_1"-->
                <!--:error-messages="checkError('phone_1')"-->
                <!--type="tel"-->
                <!--prepend-icon="phone"-->
                <!--placeholder="(000) 000 - 0000"-->
                <!--mask="phone"-->
                <!--&gt;</v-text-field>-->
                <!--</v-flex>-->
                <!--<v-flex-->
                <!--xs12 sm4 md4-->
                <!--&gt;-->
                <!--<v-text-field-->
                <!--v-model="editedItem.phone_2"-->
                <!--:error-messages="checkError('phone_2')"-->
                <!--type="tel"-->
                <!--prepend-icon="phone"-->
                <!--placeholder="(000) 000 - 0000"-->
                <!--mask="phone"-->
                <!--&gt;</v-text-field>-->
                <!--</v-flex>-->
                <!--<v-flex-->
                <!--xs12 sm4 md4-->
                <!--&gt;-->
                <!--<v-text-field-->
                <!--v-model="editedItem.phone_3"-->
                <!--:error-messages="checkError('phone_3')"-->
                <!--type="tel"-->
                <!--prepend-icon="phone"-->
                <!--placeholder="(000) 000 - 0000"-->
                <!--mask="phone"-->
                <!--&gt;</v-text-field>-->
                <!--</v-flex>-->

                <!--<v-flex xs12 sm12 md12>-->
                <!--<v-text-field-->
                <!--v-model="editedItem.notation"-->
                <!--:error-messages="checkError('notation')"-->
                <!--prepend-icon="notes"-->
                <!--placeholder="Примечание"-->
                <!--&gt;</v-text-field>-->
                <!--</v-flex>-->
                <!--</v-layout>-->
                <!--</v-container>-->
                <!--<v-card-actions>-->
                <!--<v-spacer></v-spacer>-->
                <!--<v-btn color="error" @click="dialog = false">-->
                <!--Отмена-->
                <!--&lt;!&ndash;<v-icon dark right>block</v-icon>&ndash;&gt;-->
                <!--</v-btn>-->
                <!--<v-btn-->
                <!--:disabled="loadOnBtn"-->
                <!--:loading="loadOnBtn"-->
                <!--class="white&#45;&#45;text main_color-bg"-->
                <!--@click="save"-->
                <!--&gt;-->
                <!--Сохранить-->
                <!--&lt;!&ndash;<v-icon dark right>check_circle</v-icon>&ndash;&gt;-->
                <!--</v-btn>-->
                <!--</v-card-actions>-->
                <!--</v-card>-->
                <!--</v-dialog>-->
            </v-toolbar>
            <!--ДОБАВЛЕНИЕ НОВЫХ ЗАПИСЕЙ В ТАБЛИЦУ-->
            <keep-alive v-if="openedComponent">
                <component v-bind:is="dynamicComponent"></component>
            </keep-alive>

            <!--CargoTable  и DebtsTable КОМПОНЕНТЫ-->
            <keep-alive>
                <component v-bind:is="dynamicTableComponent"></component>
            </keep-alive>
        </v-container>
    </div>
</template>
<script>
    import CargoProfit from '~/components/Cargo/CargoProfit';
    import CargoDebts from '~/components/Cargo/CargoDebts';
    import CargoTable from '~/components/Cargo/CargoTable';
    import DebtsTable from '~/components/Cargo/DebtsTable';
    import CargoDebtsNav from '~/components/Cargo/CargoDebtsNav';
    import ControlPanel from '~/components/Cargo/Control/ControlPanel';
    import { mapGetters } from 'vuex';
    import axios from 'axios';
    import Search from '~/components/Search';

    export default {
        // Сохранение имен клиентов в cargo store
        async fetch ({store}) {
            const { data } = await axios.get('/clientNames').catch((errors) => {
                console.error('Ошибка при запросе клиентов', errors);
            });
            const { clientsNames } = data;
            const names = _.map(clientsNames, 'name');

            store.commit('cargo/SET_CLIENTSNAMES', names);
        },
        components: {
            CargoProfit,
            CargoDebts,
            CargoTable,
            CargoDebtsNav,
            ControlPanel,
            Search,
            DebtsTable
        },
        middleware: 'auth',
        head () {
            return { title: `Карго и Долги` };
        },
        data: () => ({
            navElem: null,
            search: ''
        }),
        watch: {
            search (val) {
                this.$store.commit('controlPanel/SET_SEARCH', val);
            }
        },
        computed: {
            ...mapGetters({
                currentTable: 'cargo/getCurrentTable',
                openedComponent: 'controlPanel/getOpenedComponent',
                countObject: 'cargo/countObject'
            }),
            // Добавление оплат и долгов в таблицу cargos
            dynamicComponent () {
                if (this.openedComponent === 'ОПЛАТА' && this.currentTable === 'КАРГО') {
                    return 'CargoProfit';
                } else if (this.openedComponent === 'ДОЛГ' && this.currentTable === 'КАРГО') {
                    return 'CargoDebts';
                }
            },
            // Смена таблиц КАРГО и ДОЛГИ
            dynamicTableComponent () {
                if (this.currentTable === 'КАРГО') {
                    return 'CargoTable';
                } else {
                    return 'DebtsTable';
                }
            },
            suman(){
                return this.suma;
            }
        },
        methods: {
            clickNav (type) {
                this.$store.commit('controlPanel/SET_OPENEDCOMPONENT', type);
            }
        }
    }
</script>
