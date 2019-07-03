<template>
    <div
        data-vue-component-name="Baza"
        class="main"
    >
        <v-container>
            <v-layout row wrap >
                <v-flex xs12 sm6 md6 class="flex justify-center">
                    <Search :value.sync="search"/>
                </v-flex>
                <v-flex xs12 sm3 md3 class="flex justify-center">
                    <v-btn
                        v-show="itemsForUpdate.length"
                        color="success"
                        @click="updateData(itemsForUpdate)"
                    >
                        Сохранить
                    </v-btn>
                </v-flex>
                <v-flex xs12 sm3 md3 class="flex justify-center">
                    <v-btn
                        v-show="selected.length"
                        color="error"
                        @click="destroyData(selected)"
                    >
                        Удалить
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-container>
        <v-data-table
            v-model="selected"
            :headers="headers"
            :items="items"
            :search="search"
            :loading="!items.length"
            item-key="id"
            select-all
            class="elevation-1"
        >
            <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
            <template v-slot:items="props">
                <td>
                    <v-checkbox
                        v-model="props.selected"
                        primary
                        hide-details
                    ></v-checkbox>
                </td>
                <td class="text-xs-center">
                    <v-edit-dialog
                        :return-value.sync="props.item.name"
                        large
                        lazy
                        persistent
                        @save="addToUpdateArray(props.item)"
                    >
                        <div>{{ props.item.name }}</div>
                        <template v-slot:input>
                            <div class="mt-3 title">Имя</div>
                        </template>
                        <template v-slot:input>
                            <v-text-field
                                v-model="props.item.name"
                                label="Имя"
                                single-line
                                counter
                                autofocus
                            ></v-text-field>
                        </template>
                    </v-edit-dialog>
                </td>
                <td class="text-xs-center">
                    <v-edit-dialog
                        :return-value.sync="props.item.phone"
                        large
                        lazy
                        persistent
                        @save="addToUpdateArray(props.item)"
                    >
                        <div>{{ props.item.phone }}</div>
                        <template v-slot:input>
                            <div class="mt-3 title">Телефон</div>
                        </template>
                        <template v-slot:input>
                            <v-text-field
                                v-model="props.item.phone"
                                label="Телефон"
                                single-line
                                counter
                                autofocus
                            ></v-text-field>
                        </template>
                    </v-edit-dialog>
                </td>
                <td class="text-xs-center">
                    <v-edit-dialog
                        :return-value.sync="props.item.code"
                        large
                        lazy
                        persistent
                        @save="addToUpdateArray(props.item)"
                    >
                        <div>{{ props.item.code }}</div>
                        <template v-slot:input>
                            <div class="mt-3 title">Код</div>
                        </template>
                        <template v-slot:input>
                            <v-text-field
                                v-model="props.item.code"
                                label="Код"
                                single-line
                                counter
                                autofocus
                            ></v-text-field>
                        </template>
                    </v-edit-dialog>
                </td>
                <td class="text-xs-center">
                    <v-edit-dialog
                        :return-value.sync="props.item.city"
                        large
                        lazy
                        persistent
                        @save="addToUpdateArray(props.item)"
                    >
                        <div>{{ props.item.city }}</div>
                        <template v-slot:input>
                            <div class="mt-3 title">Город</div>
                        </template>
                        <template v-slot:input>
                            <v-text-field
                                v-model="props.item.city"
                                label="Город"
                                single-line
                                counter
                                autofocus
                            ></v-text-field>
                        </template>
                    </v-edit-dialog>
                </td>
                <td class="text-xs-center">
                    <v-edit-dialog
                        :return-value.sync="props.item.notify"
                        large
                        lazy
                        persistent
                        @save="addToUpdateArray(props.item)"
                    >
                        <div>{{ props.item.notify | convertCommonItems }}</div>
                        <template v-slot:input>
                            <div class="mt-3 title">Оповещать</div>
                        </template>
                        <template v-slot:input>
                            <v-select
                                v-model="props.item.notify"
                                :items="commonItems"
                                item-text="title"
                                item-value="id"
                                hide-selected
                                label="Оповещать"
                                single-line
                            ></v-select>
                        </template>
                    </v-edit-dialog>
                </td>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    import axios from 'axios';
    import { mapGetters } from 'vuex';

    export default {
        name: 'Baza',
        components: {
            Search: () => import('~/components/Search'),
        },
        data () {
            return {
                search: '',
                selected: [],
                headers: [
                    {
                        text: 'Имя',
                        value: 'name',
                    },
                    {
                        text: 'Телефон',
                        value: 'phone',
                    },
                    {
                        text: 'Код',
                        value: 'code',
                    },
                    {
                        text: 'Город',
                        value: 'city',
                    },
                    {
                        text: 'Оповещать',
                        value: 'notify',
                    },
                ],
                items: [],
                itemsForUpdate: [],
            };
        },
        computed: {
            ...mapGetters({
                commonItems: 'settings/commonItems',
            }),
        },
        async created () {
            try {
                const { data } = await axios.get('baza/all');
                // console.log('DFD', data);
                const { baza = [] } = data;
                this.items = baza;
            } catch (e) {
                console.error(`Ошибка при получении базы данных - ${e}`);
            }
        },
        methods: {
            addToUpdateArray (item) {
                console.log('item', item);
                const finded = _.find(this.itemsForUpdate, { id: item.id });
                if (finded) {
                    const findedIndex = _.findIndex(this.itemsForUpdate, { id: item.id });
                    if (findedIndex !== -1) {
                        this.itemsForUpdate.splice(findedIndex, 1, item);
                    }
                } else {
                    this.itemsForUpdate.push(item);
                }
            },
            async updateData (arr) {
                if (!_.isEmpty(arr)) {
                    try {
                        await axios.post('baza/update', arr);
                        this.itemsForUpdate = [];

                        this.$snotify.success('Данные успешно обновлены', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    } catch (e) {
                        console.error(`Ошибка при обновлении базы данных - ${e}`);
                    }
                }
            },
            async destroyData (arr) {
                this.itemsForUpdate = [];
                if (!_.isEmpty(arr)) {
                    const ids = _.map(arr, 'id');
                    try {
                        await axios.post('baza/destroy', ids);
                        _.forEach(ids, (item) => {
                            const index = _.findIndex(this.items, { id: item.id });
                            this.items.splice(index, 1);
                        });
                        _.remove(this.items, elem => _.includes(ids, elem.id));

                        this.$snotify.success('Данные успешно удалены', {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    } catch (e) {
                        console.error(`Ошибка при удалении записи базы данных - ${e}`);
                    }
                }
            },
        },
    };
</script>
