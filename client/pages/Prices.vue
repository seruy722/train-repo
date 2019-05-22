<template>
    <div
        data-vue-component-name="Prices"
        class="main"
    >
        <Search :value.sync="search" class="mb-3"/>
        <v-expansion-panel>
            <v-expansion-panel-content
                v-for="(item, i) in items"
                v-show="item.show"
                :key="i"
                ripple
            >
                <template v-slot:actions>
                    <v-icon color="teal">done</v-icon>
                </template>
                <template v-slot:header>
                    <div>{{ item.name }}</div>
                </template>
                <v-data-table
                    :headers="headers"
                    :items="item.children"
                    class="elevation-1"
                    hide-actions
                >
                    <template v-slot:items="props">
                        <td class="text-xs-center">{{ props.item.for_kg }}</td>
                        <td class="text-xs-center">{{ props.item.for_place }}</td>
                        <td class="text-xs-center">{{ props.item.for_kg_brand }}</td>
                        <td class="text-xs-center">{{ props.item.for_place_brand }}</td>
                    </template>
                </v-data-table>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </div>
</template>

<script>
    import axios from 'axios';
    import { universalSort } from '~/utils';

    export default {
        name: 'Prices',
        components: {
            Search: () => import('~/components/Search'),
        },
        data () {
            return {
                items: [],
                headers: [
                    { text: 'for_kg', align: 'center', sortable: false, value: 'for_kg' },
                    { text: 'for_place', align: 'center', sortable: false, value: 'for_place' },
                    { text: 'for_kg_brand', align: 'center', sortable: false, value: 'for_kg_brand' },
                    { text: 'for_place_brand', align: 'center', sortable: false, value: 'for_place_brand' },
                ],
                search: '',
            };
        },
        watch: {
            search (val) {
                this.filter(this.items, val);
            },
        },

        async asyncData () {
            const { data } = await axios.get('prices/all');
            const func = ((data) => {
                const arr = [];
                _.forEach(data, (value, key) => {
                    arr.push({
                        name: key,
                        show: true,
                        children: value,
                    });
                });
                return universalSort(arr);
            });

            return { items: func(data.priceData) };
        },
        methods: {
            filter: _.debounce(function (data, search) {
                if (!search) {
                    _.forEach(this.items, (item) => {
                        item.show = true;
                    });
                } else {
                    _.forEach(this.items, (item) => {
                        if (_.includes(item.name, search)) {
                            item.show = true;
                        } else {
                            item.show = false;
                        }
                    });
                }
                // console.log(this.$ls.get('someNumber'));
            }, 300),
        },
    };
</script>
