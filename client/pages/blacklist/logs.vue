<template>
    <div class="main" data-component-name="Logs">
        <v-toolbar color="white" dark>
            <v-toolbar-title class="text-xs-center title blue--text">Логи
            </v-toolbar-title>
        </v-toolbar>
        <v-toolbar flat color="white">

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
            <template>
                <v-alert slot="no-results" :value="true" color="error" icon="warning">
                    Ваш поиск по "{{ search }}" не дал результатов.
                </v-alert>
            </template>
            <template slot="no-data">
                <v-alert :value="true" color="error" icon="warning">
                    Таблица пустая
                </v-alert>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    import axios from 'axios';
    import Search from '~/components/Search';
    import { formatDate } from '~/utils';

    export default {
        middleware: 'auth',
        components: {
            Search
        },
        async asyncData () {
            const { data } = await axios.get('getLogList').catch(() => {
                console.error('Ошибка при сохранении логов!');
            });
            const { list } = data;
            const formatList = formatDate(list, 'YYYY-MM-DD HH:mm:ss', 'DD-MM-YYYY');

            return { list: formatList };
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
