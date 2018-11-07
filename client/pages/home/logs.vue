<template>
    <div class="main" data-component-name="Logs">
        <v-toolbar flat color="white">
            <v-toolbar-title class="blue--text headline">Логирование действий пользователей</v-toolbar-title>
            <v-divider
                class="mx-2"
                inset
                vertical
            ></v-divider>
            <v-spacer></v-spacer>

            <!--ПОИСК-->
            <search :value.sync="search"></search>

        </v-toolbar>
        <v-data-table
            :headers="headers"
            :items="list"
            :search="search"
            class="elevation-1"
        >
            <v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>
            <template slot="items" slot-scope="props">
                <tr>
                    <td class="text-xs-center">{{ props.item.created_at }}</td>
                    <td class="text-xs-center">{{ props.item.fio }}</td>
                    <td class="text-xs-center">{{ props.item.phone_1 }}</td>
                    <td class="text-xs-center">{{ props.item.phone_2 }}</td>
                    <td class="text-xs-center">{{ props.item.phone_3 }}</td>
                    <td class="text-xs-center">{{ props.item.notation }}</td>
                    <td class="text-xs-center">{{ props.item.action }}</td>
                    <td class="text-xs-center">{{ props.item.user_fio_id }}</td>
                </tr>
            </template>
            <v-alert slot="no-results" :value="true" color="error" icon="warning">
                Ваш поиск по "{{ search }}" не дал результатов.
            </v-alert>
        </v-data-table>
    </div>
</template>

<script>
    import axios from 'axios';
    import moment from 'moment';
    import Search from '~/components/Search';

    export default {
        components: {
            Search
        },
        async asyncData (context) {
            const { data } = await axios.get('getLogList').catch(() => {
                console.error('Ошибка при сохранении логов!');
            });
            const { list } = data;
            _.forEach(list, (item) => {
                item.created_at = moment(item.created_at, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY');
            });
            return { list };
        },
        head () {
            return {title: `Логи`};
        },
        data () {
            return {
                search: '',
                headers: [
                    {
                        text: 'Дата',
                        align: 'center',
                        value: 'created_at'
                    },
                    {
                        text: 'ФИО',
                        align: 'center',
                        value: 'fio'
                    },
                    {text: 'Телефон', align: 'center', value: 'phone_1'},
                    {text: 'Телефон', align: 'center', value: 'phone_2'},
                    {text: 'Телефон', align: 'center', value: 'phone_3'},
                    {text: 'Примечание', align: 'center', value: 'notation'},
                    {text: 'Действие', align: 'center', value: 'action'},
                    {text: 'Пользователь', align: 'center', value: 'user_fio_id'},
                ]
            }
        }
    }
</script>

<style scoped>
    .main {
        width: 95%;
    }
</style>
