<template>
    <div
        data-vue-component-name="ControlPanelFaxEntries"
        class="fax_control_panel"
    >
        <template>
            <v-layout row justify-center>
                <v-dialog v-model="dialogFaxNames" max-width="300px">
                    <v-card>
                        <v-card-title>Выбор факса</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text style="height: 100px;">
                            <v-combobox
                                v-model.lazy="selectedFaxNames"
                                :items="getFaxesNames"
                                :rules="[v => !!v || 'Обьязательное поле.']"
                                item-text="fax_name"
                                item-value="id"
                                label="Факс"
                                hide-selected
                                return-object
                            ></v-combobox>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn color="blue darken-1" flat @click.stop="dialogFaxNames = false">Закрыть</v-btn>
                            <v-btn color="blue darken-1" flat @click.stop="moveFaxEntries">Переместить</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-layout>
        </template>

        <div class="fax_control_panel__button_wraper">
            <div v-if="move" class="fax_control_panel__button">
                <v-tooltip bottom>

                    <template v-slot:activator="{ on }">
                        <v-icon
                            dark
                            color="cyan"
                            v-on="on"
                            @click.stop="openDialogFaxNames"
                        >
                            open_with
                        </v-icon>
                    </template>

                    <span>Переместить</span>
                </v-tooltip>
            </div>

            <div v-if="destroy" class="fax_control_panel__button">
                <v-tooltip bottom>

                    <template v-slot:activator="{ on }">
                        <v-icon
                            dark
                            color="red"
                            v-on="on"
                            @click.stop="destroyEntries"
                        >
                            delete
                        </v-icon>
                    </template>

                    <span>Удалить</span>
                </v-tooltip>
            </div>

        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import axios from 'axios';

    export default {
        name: 'ControlPanelFaxEntries',
        props: {
            destroy: {
                type: Boolean,
                default: false,
            },
            move: {
                type: Boolean,
                default: false,
            },
            many: {
                type: Boolean,
                default: false,
            },
            item: {
                type: Object,
                default: () => ({}),
            },
        },
        data () {
            return {
                dialogFaxNames: false,
                selectedFaxNames: [],
            };
        },
        computed: {
            ...mapGetters({
                getFaxesNames: 'fax/getFaxesNames',
            }),
        },
        methods: {
            openDialogFaxNames () {
                this.dialogFaxNames = true;
                this.faxNamesQuery();
            },
            async faxNamesQuery () {
                if (_.isEmpty(this.$store.getters['fax/getFaxesNames'])) {
                    try {
                        // Запрос данных по названиям факса
                        const { data } = await axios.get('faxes/faxesNames');
                        const { faxesNames = [] } = data;

                        this.$store.dispatch('fax/setFaxNames', faxesNames);
                    } catch (e) {
                        console.error(`Произошла ошибка при запросе данных названий факса - ${e}`);
                    } finally {
                        console.log('Completed request for get fax names.');
                    }
                }
            },
            async moveFaxEntries () {
                const { entriesID, clientID } = this.prepareData();

                try {
                    const { data } = await axios.post('faxes/moveEntries', {
                        entriesID,
                        faxID: this.selectedFaxNames.id,
                    });
                    const { status } = data;

                    if (status) {
                        this.dialogFaxNames = false;

                        this.$snotify.success(`Записи успешно перемещены в "${this.selectedFaxNames.fax_name}"`, {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });

                        this.$store.dispatch('fax/destroyEntriesFromGroupedData', {
                            entriesID,
                            clientID,
                        });
                    }
                } catch (error) {
                    console.error(`Ошибка при перемещении записей факса - ${error}`);
                } finally {
                    console.log('Request move fax entries completed.');
                }
            },
            destroyEntries () {
                this.$snotify.confirm(`Удалить записи клиента - "${this.item.name}"`, {
                    timeout: 5000,
                    showProgressBar: true,
                    closeOnClick: false,
                    pauseOnHover: true,
                    buttons: [
                        {
                            text: 'Yes', action: (toast) => {
                                this.$snotify.remove(toast.id);
                                this.destroyFaxEntries();
                            }
                        },
                        {
                            text: 'No', action: (toast) => {
                                this.$snotify.remove(toast.id);
                            }
                        },
                    ],
                });
            },
            prepareData () {
                let entriesID = [];
                const clientID = this.item.client_id;

                if (!this.many) {
                    entriesID.push(this.item.id);
                } else {
                    const { clientItemsArray } = this.item;
                    entriesID = _.map(clientItemsArray, item => item.id);
                }

                return {
                    entriesID,
                    clientID,
                };
            },
            async destroyFaxEntries () {
                const { entriesID, clientID } = this.prepareData();

                try {
                    const { data } = await axios.post('faxes/destroyFaxData', { entriesID });
                    const { status } = data;

                    if (status) {
                        this.$store.dispatch('fax/destroyEntriesFromGroupedData', {
                            entriesID,
                            clientID,
                        });

                        this.$snotify.success(`Записи клиента < ${this.item.name} > успешно удалены.`, {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    }
                } catch (error) {
                    console.error(`Ошибка при удалении записей факса - ${error}`);
                } finally {
                    console.log('Request delete fax entries completed.');
                }
            },
        },
    };
</script>

<style lang="scss">
    .fax_control_panel {
        background-color: #ebeef0;

        .fax_control_panel__button_wraper {
            width: 70px;
            display: flex;
            justify-content: space-between;

            .fax_control_panel__button:hover{
                background-color: #dde0e2;
            }
        }
    }

</style>
