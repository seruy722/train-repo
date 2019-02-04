<template>
    <div
        class="main"
        data-component-name="Faxes"
    >
        <v-container fluid>

            <v-toolbar>
                <v-toolbar-title class="text-xs-center title blue--text">Факсы</v-toolbar-title>
                <v-spacer></v-spacer>
                <Search :value.sync="search"></Search>
            </v-toolbar>

            <v-toolbar>
                <v-toolbar-items v-show="selected.length">
                    <v-flex xs12>
                        <v-btn outline fab small color="indigo">
                            <v-icon dark>edit</v-icon>
                        </v-btn>
                    </v-flex >

                    <v-flex xs12>
                        <v-btn outline fab small color="error">
                            <v-icon dark>delete</v-icon>
                        </v-btn>
                    </v-flex>
                </v-toolbar-items>
            </v-toolbar>

            <v-data-table
                v-model="selected"
                :headers="headers"
                :search="search"
                :items="faxesData"
                :pagination.sync="pagination"
                select-all
                item-key="id"
                disable-initial-sort
                class="elevation-1"
            >
                <template slot="headers" slot-scope="props">
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
                <template slot="items" slot-scope="props">
                    <tr
                        :class="props.item.uploaded_to_table_cargos ? 'tr_green__bg' : 'tr_red__bg'"
                        :active="props.selected" @click="props.selected = !props.selected"
                        @mouseover="mouseoverOnTrOfTable"
                    >
                        <td>
                            <v-checkbox
                                :input-value="props.selected"
                                primary
                                hide-details
                            ></v-checkbox>
                        </td>
                        <td class="text-xs-center">{{ props.item.fax_name }}</td>
                        <td class="text-xs-center">{{ props.item.upload_date }}</td>
                        <td class="text-xs-center">{{ props.item.uploaded_to_table_cargos_date ? props.item.uploaded_to_table_cargos_date : '----' }}</td>
                        <td class="text-xs-center">{{ props.item.uploaded_to_table_cargos ? 'Да': 'Нет' }}</td>
                        <td class="text-xs-center">{{ props.item.created_at }}</td>
                    </tr>
                </template>

                <template slot="no-results">
                    <v-alert :value="true" color="error" icon="warning">
                        Поиск по "{{ search }}" не дал результатов.
                    </v-alert>
                </template>

            </v-data-table>
        </v-container>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import axios from 'axios';
    import Search from '~/components/Search';


    export default {
        asyncData () {
            return axios.get('faxes')
                .then(res => ({ faxesData: res.data.faxesData }));
        },
        name: 'Faxes',
        components: {
            Search,
        },
        data: () => ({
            search: '',
            pagination: {
                sortBy: 'name',
            },
            selected: [],
            headers: [
                { text: 'Название факса', align: 'center', value: 'fax_name' },
                { text: 'Дата выезда', align: 'center', value: 'upload_date' },
                { text: 'Дата загрузки в базу', align: 'center', value: 'uploaded_to_table_cargos_date' },
                { text: 'Загружен в базу', align: 'center', value: 'uploaded_to_table_cargos' },
                { text: 'Дата добавления', align: 'center', value: 'created_at' },
            ],
        }),

        methods: {
            toggleAll () {
                if (this.selected.length) {
                    this.selected = [];
                } else {
                    this.selected = this.faxesData.slice();
                }
            },
            changeSort (column) {
                if (this.pagination.sortBy === column) {
                    this.pagination.descending = !this.pagination.descending
                } else {
                    this.pagination.sortBy = column
                    this.pagination.descending = false
                }
            },
            mouseoverOnTrOfTable () {
                console.log('HOVER');
            },
        },
    };

</script>
