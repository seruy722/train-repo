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
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Поиск"
                single-line
                hide-details
            ></v-text-field>
        </v-toolbar>
        <v-data-table
            :headers="headers"
            :items="getLogsList"
            :loading="isLoad"
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

    export default {
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
                ],
                logList: [],
                isLoad: false,
            }
        },
        computed: {
            getLogsList () {
                if (this.logList.length) {
                    _.forEach(this.logList, (item) => {
                        console.log(item.created_at);
                        item.created_at = moment(item.created_at, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY HH:mm:ss');
                    });
                    return this.logList;
                }
            }
        },
        created () {
            this.fetchLogList();
        },
        methods: {
            async fetchLogList () {
                this.isLoad = true;
                await axios.get('getLogList').then((response) => {
                    const { data } = response;
                    this.logList = data.list;
                    this.isLoad = false;
                }).catch(() => {
                    console.error('Ошыбка при сохранении логов!');
                });
            }
        }
    }
</script>

<style>
    .main {
        width: 95%;
    }
</style>
