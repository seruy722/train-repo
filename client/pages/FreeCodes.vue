<template>
    <div
        data-vue-component-name="FreeCodes"
        class="main"
    >
        <v-layout row wrap>
            <v-flex xs12 sm12 md12>
                <v-card>
                    <v-card-title primary-title>
                        <div>
                            <h3>Введити число до которого нужно вывести свободные кода клиентов</h3>
                        </div>
                    </v-card-title>

                    <v-card-actions>
                        <v-layout row wrap>
                            <v-flex xs6 sm4 md3>
                                <v-text-field
                                    v-model.number="to"
                                    type="number"
                                    label="Число"
                                    autofocus
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs6 sm4 md3 class="ml-3">
                                <v-btn color="success" @click="getFreeCodes">Показать</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-actions>
                </v-card>

                <v-card>
                    <v-card-title primary-title>
                        <div>
                            <h3>Введите код клиента и нажмите кнопку добавить</h3>
                        </div>
                    </v-card-title>

                    <v-card-actions>
                        <v-layout row wrap>
                            <v-flex xs6 sm4 md3>
                                <v-text-field
                                    v-model="clientCode"
                                    :error-messages="checkError('clientCode')"
                                    label="Код"
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs6 sm4 md3 class="ml-3">
                                <v-btn color="success" @click="addClient(clientCode)">Добавить</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>

        <v-layout v-show="items.length" row wrap>
            <v-list v-for="(item, i) in items" :key="i" dense>
                <v-list-tile>
                    <v-list-tile-content class="align-end code_list">{{ item }}</v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-layout>
    </div>
</template>

<script>
    import axios from 'axios';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        name: 'FreeCodes',
        mixins: [checkErrorMixin],
        data () {
            return {
                to: 0,
                items: [],
                limit: 3000,
                clientCode: '',
                er: {},
            };
        },
        methods: {
            async getFreeCodes () {
                try {
                    const { data } = await axios.post('users/freecodes', { to: this.to });
                    const { items = [] } = data;
                    if (items.length > this.limit) {
                        this.items = items.slice(0, this.limit);
                    } else {
                        this.items = items;
                    }

                    this.$snotify.success(`Найдено: ${items.length} элементов`, {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                    });
                } catch (e) {
                    console.error('Ошибка при получении кодов');
                }
            },
            async addClient (code) {
                this.changeErrors({});
                const clientCode = _.trim(code);
                if (clientCode) {
                    try {
                        const { data } = await axios.post('users/add', { clientCode });
                        const { client = null } = data;

                        if (client) {
                            this.$snotify.success(`Клиент: ${client} успешно добавлен`, {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });

                            this.clientCode = '';
                        } else {
                            this.$snotify.warning(`Клиент: ${clientCode} уже есть в базе`, {
                                timeout: 3000,
                                showProgressBar: true,
                                closeOnClick: true,
                                pauseOnHover: true,
                            });
                        }
                    } catch (errors) {
                        this.changeErrors(errors.response.data.errors);
                        console.error(`Ошибка при добавлении клиента - ${errors.response.data.errors}`);
                    }
                }
            },
        },
    };
</script>

<style lang="stylus">
    .code_list {
        /*border: 1px solid blue;*/
        font-weight: bold;
    }
</style>
