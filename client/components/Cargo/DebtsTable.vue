<template>
    <div data-vue-component="DebtsTable">
        <v-container>
            <span>Сумма: {{countObject.sum}}</span>
            <span>Комиссия: {{countObject.commission}}</span>
        </v-container>

        <v-data-table
            v-model="selected"
            :headers="headers"
            :items="debtsList"
            :search="search"
            :loading="progressLoading"
            disable-initial-sort
            item-key="id"
            select-all
            class="elevation-1"
        >
            <v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>

            <template slot="items" slot-scope="props">
                <tr :class="props.item.type === 'ДОЛГ' ? 'tr_debts__bg' : 'tr_profit__bg'">
                    <td class="text-xs-center">
                        <v-checkbox
                            v-model="props.selected"
                            primary
                            hide-details
                        ></v-checkbox>
                    </td>
                    <td>{{ props.item.created_at }}</td>
                    <td class="text-xs-center">{{ props.item.type }}</td>
                    <td class="text-xs-center">{{ props.item.sum }}</td>
                    <td class="text-xs-center">{{ props.item.commission }}</td>
                    <td class="text-xs-center">{{ props.item.client }}</td>
                    <td class="text-xs-center">{{ props.item.notation }}</td>
                </tr>
            </template>

            <template slot="no-results">
                <v-alert :value="true" color="error" icon="warning">
                    Поиск по "{{ search }}" не дал результатов.
                </v-alert>
            </template>

            <template slot="no-data">
                <v-alert v-if="progressLoading" :value="true" color="primary" icon="info">
                    <span>Загрузка...</span>
                </v-alert>

                <v-alert v-else :value="true" color="error" icon="warning">
                    <span>Таблица пустая</span>
                </v-alert>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import axios from 'axios';
    import {formatDate} from '~/utils';
    import progressLoadingTable from '~/mixins/progressLoadingTable';

    export default {
        name: 'DebtsTable',
        mixins: [progressLoadingTable],
        data: () => ({
            selected: [],
            headers: [
                {
                    text: 'Дата',
                    align: 'center',
                    value: 'created_at'
                },
                {text: 'Тип', align: 'center', value: 'type'},
                {text: 'Сумма', align: 'center', value: 'sum'},
                {text: 'Комиссия', align: 'center', value: 'commission'},
                {text: 'Клиент', align: 'center', value: 'client'},
                {text: 'Примечания', align: 'center', value: 'notation'},
            ],
        }),
        created () {
            this.fetch();
            console.log('created');
        },
        computed: {
            ...mapGetters({
                debtsList: 'cargo/debtsList',
                search: 'controlPanel/getSearch',
                countObject: 'cargo/countObject'
            }),
        },
        methods: {
            // Запрос данных с сервера
            async fetch () {
                const {data} = await axios.get('/debts');
                const { debtsList } = data;

                this.$store.commit('cargo/SET_DEBTS_LIST', debtsList);
                this.$store.commit('cargo/CHANGE_CARGOLIST');
                this.$store.dispatch('cargo/calcData');
                this.stopProgressbarOnTable();
            }
        }
    }
</script>


<style lang="scss" scoped>
    @import '~assets/sass/variables';

    tr.tr_profit__bg {
        background-color: $tr_profit__bg;
    }

    tr.tr_debts__bg {
        background-color: $tr_debts__bg;
    }

    tr.tr_profit__bg, tr.tr_debts__bg {
        border: $tr_border;
    }
</style>
