<template>
    <div data-vue-component="CargoTable">
        <v-container>
            <span>Сумма: {{countObject.sum}}</span>
            <span>Мест: {{countObject.place}}</span>
            <span>Вес: {{countObject.kg}}</span>
            <span>Скидка: {{countObject.sale}}</span>
        </v-container>

        <v-data-table
            v-model="selected"
            :headers="headers"
            :items="cargoList"
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
                    <td class="text-xs-center">{{ props.item.sale }}</td>
                    <td class="text-xs-center">{{ props.item.client }}</td>
                    <td class="text-xs-center">{{ props.item.place }}</td>
                    <td class="text-xs-center">{{ props.item.kg }}</td>
                    <td class="text-xs-center">{{ props.item.fax }}</td>
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

    export default {
        name: 'CargoTable',
        data: () => ({
            selected: [],
            progressLoading: true,
            headers: [
                {
                    text: 'Дата',
                    align: 'center',
                    value: 'created_at'
                },
                {text: 'Тип', align: 'center', value: 'type'},
                {text: 'Сумма', align: 'center', value: 'sum'},
                {text: 'Скидка', align: 'center', value: 'sale'},
                {text: 'Клиент', align: 'center', value: 'client'},
                {text: 'Мест', align: 'center', value: 'place'},
                {text: 'Вес', align: 'center', value: 'kg'},
                {text: 'Факс', align: 'center', value: 'fax'},
                {text: 'Примечания', align: 'center', value: 'notation'},
            ],
        }),
        created () {
            this.fetch();
        },
        computed: {
            ...mapGetters({
                cargoList: 'cargo/cargoList',
                search: 'controlPanel/getSearch',
                countObject: 'cargo/countObject'
            }),
        },
        methods: {
            // Запрос данных с сервера
            async fetch () {
                const {data} = await axios.get('/cargos');
                const {cargoList} = data;

                this.$store.commit('cargo/SET_LIST', cargoList);
                this.$store.dispatch('cargo/calcData');
                this.progressLoading = false;
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
