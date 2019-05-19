<template>
    <div
        data-vue-component-name="Sending"
        class="main"
    >
        <v-data-table
            v-model="selected"
            :headers="headers"
            :items="sendingData"
            :pagination.sync="pagination"
            select-all
            item-key="name"
            class="elevation-1"
        >
            <template v-slot:headers="props">
                <tr>
                    <th>
                        <v-checkbox
                            :input-value="props.all"
                            :indeterminate="props.indeterminate"
                            primary
                            hide-details
                            @click.stop="toggleAll"
                        ></v-checkbox>
                    </th>
                    <th
                        v-for="header in props.headers"
                        :key="header.text"
                        :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '']"
                        @click="changeSort(header.value)"
                    >
                        <v-icon small>arrow_upward</v-icon>
                        {{ header.text }}
                    </th>
                </tr>
            </template>
            <template v-slot:items="props">
                <tr :active="props.selected" @click="props.selected = !props.selected">
                    <td>
                        <v-checkbox
                            :input-value="props.selected"
                            primary
                            hide-details
                        ></v-checkbox>
                    </td>
                    <td class="text-xs-center">{{ props.item.name }}</td>
                    <td class="text-xs-center">{{ `${message} ${props.item.sum}` }}</td>
                    <td class="justify-center layout px-0">
                        <!--<v-icon-->
                            <!--medium-->
                            <!--class="mr-2"-->
                            <!--color="teal"-->
                            <!--@click="editItem(props.item)"-->
                        <!--&gt;-->
                            <!--edit-->
                        <!--</v-icon>-->
                        <v-icon
                            medium
                            color="red"
                            @click="deleteItem(props.item)"
                        >
                            delete
                        </v-icon>
                    </td>
                </tr>
            </template>
        </v-data-table>

        <v-card>
            <v-card-text>
                <v-radio-group v-model="radios" :mandatory="false">
                    <v-radio label="Немедленно" value="now"></v-radio>
                    <v-radio label="Отложить отправку sms" value="hold"></v-radio>
                </v-radio-group>
                <v-layout v-show="radios === 'hold'" row wrap>
                    <v-flex xs12 sm3 md3 class="ml-4">
                        <DatePicker
                            :value.sync="date"
                            :datePickerSettings="{}"
                        />
                    </v-flex>
                    <v-flex xs12 sm2 md2 class="ml-4">
                        <TimePicker
                            :value.sync="time"
                        />
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" @click="sendSendingFaxData">Отправить</v-btn>
                <v-btn v-show="selected.length" flat color="red">Удалить</v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        name: 'Sending',
        components: {
            DatePicker: () => import('~/components/Pickers/DatePicker'),
            TimePicker: () => import('~/components/Pickers/TimePicker'),
        },
        data: () => ({
            pagination: {
                sortBy: 'name',
            },
            selected: [],
            headers: [
                {
                    text: 'Клиент',
                    align: 'center',
                    value: 'name',
                },
                { text: 'Сообщение', align: 'center', value: 'sum' },
                { text: 'Действия', align: 'center', value: 'sum' },
            ],
            radios: 'now',
            date: new Date().toISOString().substr(0, 10),
            time: new Date().toTimeString().substr(0, 5),
        }),
        computed: {
            ...mapGetters({
                sendingData: 'sending/sendingData',
                message: 'sending/message',
            }),
        },
        fetch ({ params, store }) {
            // console.log('fetch');
            const { sendingData = [] } = params;
            if (!_.isEmpty(sendingData)) {
                store.dispatch('sending/setSendingData', sendingData);
            }
            // console.log('sendingData', sendingData);
            console.log('sendingData', sendingData);

            // return { desserts: sendingData };
        },
        methods: {
            editItem () {

            },
            deleteItem () {
                this.$store.dispatch('sending/setMessage', 'Privet');
            },
            sendSendingFaxData () {
                const answer = confirm('Отправить рассылку?');
            },
            toggleAll () {
                if (this.selected.length) {
                    this.selected = [];
                } else {
                    this.selected = this.desserts.slice();
                }
            },
            changeSort (column) {
                if (this.pagination.sortBy === column) {
                    this.pagination.descending = !this.pagination.descending;
                } else {
                    this.pagination.sortBy = column;
                    this.pagination.descending = false;
                }
            },
        },
    };
</script>
