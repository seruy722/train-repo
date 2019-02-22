<template>

    <div
        class="main"
        data-vue-component-name="FaxCounted"
    >
        <template>
            <v-toolbar>
                <v-toolbar-title>
                    <span class="font-weight-bold">{{ getFaxName }}</span>
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-toolbar-items>
                    <v-btn color="primary" dark @click="expand = !expand">
                        {{ expand ? 'Close' : 'Keep' }} other rows
                    </v-btn>
                    <v-menu bottom left>
                        <v-btn
                            slot="activator"
                            dark
                            icon
                            color="grey"
                        >
                            <v-icon>more_vert</v-icon>
                        </v-btn>

                        <v-list>
                            <v-list-tile
                                v-for="(item, i) in items"
                                :key="i"
                                @click=""
                            >
                                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                    <!--<v-btn fab dark small color="grey">-->
                        <!--<v-icon dark>more_vert</v-icon>-->
                    <!--</v-btn>-->
                    <!--<v-btn fab dark small color="green">-->
                        <!--Ex-->
                    <!--</v-btn>-->
                    <!--<v-btn fab dark small color="primary">-->
                        <!--<v-icon dark>settings</v-icon>-->
                    <!--</v-btn>-->
                </v-toolbar-items>
            </v-toolbar>
            <!--<v-container class="text-center">-->
                <!--<v-layout row wrap>-->
                    <!--<v-flex xs12 sm12 md12>-->
                        <!---->
                    <!--</v-flex>-->
                    <!--&lt;!&ndash;<v-flex xs12 sm2 md2>&ndash;&gt;-->
                        <!--&lt;!&ndash;<span>Мест: <span class="font-weight-bold">{{ totalFaxPlaces }}</span></span>&ndash;&gt;-->
                    <!--&lt;!&ndash;</v-flex>&ndash;&gt;-->

                    <!--&lt;!&ndash;<v-flex xs12 sm2 md2>&ndash;&gt;-->
                        <!--&lt;!&ndash;<span>Вес: <span class="font-weight-bold">{{ totalFaxKg }}</span></span>&ndash;&gt;-->
                    <!--&lt;!&ndash;</v-flex>&ndash;&gt;-->

                    <!--&lt;!&ndash;<v-flex xs12 sm3 md3>&ndash;&gt;-->
                        <!--&lt;!&ndash;<span>Сумма: <span class="font-weight-bold">{{ totalFaxSum }}</span></span>&ndash;&gt;-->
                    <!--&lt;!&ndash;</v-flex>&ndash;&gt;-->
                <!--</v-layout>-->
            <!--</v-container>-->
            <v-data-table
                :headers="headers"
                :items="faxData"
                :expand="expand"
                item-key="name"
                class="elevation-1"
            >
                <template slot="items" slot-scope="props">
                    <tr :class="{['table__tr-bold_text table__tr-bg_color']: props.item.brand}" @click="props.expanded = !props.expanded">
                        <td class="text-xs-center">
                            <v-badge v-if="props.item.brand" class="table__td-badge" right>
                                <span slot="badge">Бренд</span>
                                <span>{{ props.item.name }}</span>
                            </v-badge>
                            <slot v-if="!props.item.brand">{{ props.item.name }}</slot>
                        </td>
                        <td class="text-xs-center">{{ props.item.place }}</td>
                        <td class="text-xs-center">{{ props.item.kg }}</td>
                        <td class="text-xs-center table__td-color-text table__tr-bold_text">{{ props.item.brand ?
                            props.item.for_kg_brand :
                            props.item.for_kg }}
                        </td>
                        <td class="text-xs-center table__td-color-text table__tr-bold_text">{{ props.item.for_place
                            }}
                        </td>
                        <td class="text-xs-center table__tr-bold_text">{{ numFormat(props.item.sum) }}</td>
                    </tr>
                </template>
                <template slot="expand" slot-scope="props">
                    <v-layout row>
                        <v-flex xs12 sm12 md12>
                            <v-card>
                                <!--<v-toolbar color="cyan" dark>-->
                                    <!--<v-toolbar-side-icon></v-toolbar-side-icon>-->

                                    <!--<v-toolbar-title>Inbox</v-toolbar-title>-->

                                    <!--<v-spacer></v-spacer>-->

                                    <!--<v-btn icon>-->
                                        <!--<v-icon>search</v-icon>-->
                                    <!--</v-btn>-->
                                <!--</v-toolbar>-->

                                <v-list three-line>
                                    <template v-for="(item, index) in itemsList">
                                        <v-subheader
                                            v-if="item.header"
                                            :key="item.header"
                                        >
                                            {{ item.header }}
                                        </v-subheader>

                                        <v-divider
                                            v-else-if="item.divider"
                                            :key="index"
                                            :inset="item.inset"
                                        ></v-divider>

                                        <v-list-tile
                                            v-else
                                            :key="item.title"
                                            avatar
                                            @click=""
                                        >
                                            <v-list-tile-avatar>
                                                <img :src="item.avatar">
                                            </v-list-tile-avatar>

                                            <v-list-tile-content>
                                                <v-list-tile-title v-html="item.title"></v-list-tile-title>
                                                <v-list-tile-sub-title v-html="item.subtitle"></v-list-tile-sub-title>
                                            </v-list-tile-content>
                                        </v-list-tile>
                                    </template>
                                </v-list>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </template>
            </v-data-table>
        </template>
    </div>

</template>

<script>
    import axios from 'axios';
    import Cookies from 'js-cookie';
    import { numberFormat } from '~/utils';

    export default {
        name: 'FaxCounted',
        data () {
            return {
                expand: false,
                itemsList: [
                    { header: 'Today' },
                    {
                        avatar: 'https://cdn.vuetifyjs.com/images/lists/1.jpg',
                        title: 'Brunch this weekend?',
                        subtitle: "<span class='text--primary'>Ali Connors</span> &mdash; I'll be in your neighborhood doing errands this weekend. Do you want to hang out?"
                    },
                    { divider: true, inset: true },
                    {
                        avatar: 'https://cdn.vuetifyjs.com/images/lists/2.jpg',
                        title: 'Summer BBQ <span class="grey--text text--lighten-1">4</span>',
                        subtitle: "<span class='text--primary'>to Alex, Scott, Jennifer</span> &mdash; Wish I could come, but I'm out of town this weekend."
                    },
                    { divider: true, inset: true },
                    {
                        avatar: 'https://cdn.vuetifyjs.com/images/lists/3.jpg',
                        title: 'Oui oui',
                        subtitle: "<span class='text--primary'>Sandra Adams</span> &mdash; Do you have Paris recommendations? Have you ever been?"
                    },
                    { divider: true, inset: true },
                    {
                        avatar: 'https://cdn.vuetifyjs.com/images/lists/4.jpg',
                        title: 'Birthday gift',
                        subtitle: "<span class='text--primary'>Trevor Hansen</span> &mdash; Have any ideas about what we should get Heidi for her birthday?"
                    },
                    { divider: true, inset: true },
                    {
                        avatar: 'https://cdn.vuetifyjs.com/images/lists/5.jpg',
                        title: 'Recipe to try',
                        subtitle: "<span class='text--primary'>Britta Holt</span> &mdash; We should eat this: Grate, Squash, Corn, and tomatillo Tacos."
                    }
                ],
                items: [
                    { title: 'Описание' },
                    { title: 'В excel' },
                    { title: 'Настройки' },
                ],
                pagination: {},
                faxData: [],
                headers: [
                    { text: 'Клиент', align: 'center', value: 'name' },
                    { text: 'Мест', align: 'center', value: 'place' },
                    { text: 'Вес', align: 'center', value: 'kg' },
                    { text: '', sortable: false, value: 'kg' },
                    { text: '', sortable: false, value: 'sm' },
                    { text: 'Сумма', align: 'center', value: 'fax_id' },
                ],
            };
        },
        computed: {
            getFaxName () {
                return Cookies.get('faxName');
            },
        },
        async asyncData ({ params, error }) {
            const faxID = _.get(params, 'faxID') || Cookies.get('faxID');

            // console.log('faxID', faxID);

            return await axios.post('faxes/faxData', { faxID })
                .then((response) => {
                    const { data } = response;
                    const { status, groupedData } = data;
                    console.log('DATA', groupedData);

                    if (status) {
                        return { groupedData };
                    }
                })
                .catch((e) => {
                    error({ statusCode: 404, message: `Произошла ошибка при запросе данных факса - ${e}` });
                });
        },
        created () {
            this.setCookies(this.$route.params);
            this.dataAggregation();
        },
        methods: {
            dataAggregation () {
                const result = [];

                if (!_.isEmpty(this.groupedData)) {
                    _.forEach(this.groupedData, (array) => {
                        _.forEach(array, (obj) => {
                            // Запись суммы для каждого клиента
                            this.calcSumClient(obj);

                            const findedObj = _.find(result, { client_id: obj.client_id, brand: obj.brand });

                            if (_.isObject(findedObj)) {
                                _.set(findedObj, 'place', findedObj.place + obj.place);
                                _.set(findedObj, 'kg', findedObj.kg + obj.kg);
                                _.set(findedObj, 'sum', findedObj.sum + obj.sum);
                            } else {
                                result.push(obj);
                            }
                        });
                    });
                }
                this.faxData = result;
            },
            setCookies (params) {
                const { faxID, faxName } = params;

                if (faxID) {
                    Cookies.set('faxID', faxID, { expires: 1 });
                    Cookies.set('faxName', faxName, { expires: 1 });
                }
            },
            calcSumClient (obj) {
                let sum = 0;
                if (obj.brand) {
                    sum = obj.for_kg_brand * obj.kg + obj.for_place * obj.place;
                } else {
                    sum = obj.for_kg * obj.kg + obj.for_place * obj.place;
                }

                _.set(obj, 'sum', _.round(sum));
            },
            numFormat (val) {
                return numberFormat(val);
            },
        },
    };
</script>

<style lang="scss">

    .table__tr-bold_text {
        font-weight: bold !important;

        & > td {
            font-weight: bold !important;
        }
    }

    .table__tr-bg_color {
        background-color: #dde0e2;
    }

    .table__td-color-text {
        color: #ff5a4d;
        font-weight: bold;
    }

    .table__td-badge .v-badge__badge {
        width: auto;
        font-weight: normal;
        border-radius: 12px;
        padding: 0 5px;
        right: -53px;
    }
</style>
