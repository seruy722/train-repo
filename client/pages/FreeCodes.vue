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
                                <v-btn color="success" @click="checkTo">Показать</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-actions>
                </v-card>

                <v-card>
                    <v-card-actions>
                        <v-layout row wrap>
                            <v-flex xs6 sm4 md3 class="ml-3">
                                <v-btn color="success" @click="openDialog">Добавить клиента</v-btn>
                            </v-flex>
                            <DialogAddClient :open.sync="openDialogAddClient"/>
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

    export default {
        name: 'FreeCodes',
        components: {
            DialogAddClient: () => import('~/components/Dialogs/DialogAddClient'),
        },
        data () {
            return {
                to: 0,
                items: [],
                limit: 3000,
                openDialogAddClient: false,
            };
        },
        methods: {
            checkTo () {
                if (!this.to) {
                    this.$snotify.warning('Введите число!', {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                    });
                } else {
                    this.getFreeCodes();
                }
            },
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
            openDialog () {
                this.openDialogAddClient = true;
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
