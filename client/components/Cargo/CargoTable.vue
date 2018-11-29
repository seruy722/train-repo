<template>
    <v-data-table
        v-model="selected"
        :headers="headers"
        :items="cargoList"
        :search="search"
        disable-initial-sort
        item-key="id"
        select-all
        class="elevation-1"
    >
        <template slot="items" slot-scope="props">
            <tr :class="props.item.type === 'ДОЛГ' ? 'bg_red' : 'bg_green'">
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
            <v-alert :value="true" color="error" icon="warning">
                Таблица пустая
            </v-alert>
        </template>
    </v-data-table>
</template>

<script>
    import { mapGetters } from 'vuex';
    import axios from 'axios';
    import { formatDate } from '~/utils';

    export default {
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
                search: 'controlPanel/getSearch'
            }),
        },
        methods: {
            async fetch () {
                const { data } = await axios.get('/cargos');
                const { cargoList } = data;

                const formatedList = formatDate(cargoList, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

                this.$store.commit('cargo/SET_LIST', formatedList);
            }
        }
    }
</script>


<style>
    tr.bg_green {
        background-color: #5ccd61;
    }
    tr.bg_red {
        background-color: #ff5a4d;
    }
    tr.bg_green, tr.bg_red {
        border: 2px solid black;
    }
</style>
