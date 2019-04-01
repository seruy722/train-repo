<template>
    <div
        data-vue-component-name="ControlPanelFaxes"
        class="faxes_control_panel"
    >
        <div class="faxes_control_panel__button_wraper">
            <div v-if="edit" class="faxes_control_panel__button">
                <v-tooltip bottom>

                    <template v-slot:activator="{ on }">
                        <v-btn v-on="on" flat icon color="cyan" @click.stop="openCloseFaxesExpandPanel">
                            <v-icon>edit</v-icon>
                        </v-btn>
                    </template>

                    <span>Редактировать</span>
                </v-tooltip>
            </div>

            <div v-if="download" class="faxes_control_panel__button">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn v-on="on" flat icon color="green" @click.stop="downloadExcle">
                            <v-icon>get_app</v-icon>
                        </v-btn>
                    </template>

                    <span>Excel</span>
                </v-tooltip>
            </div>

            <div v-if="destroy" class="faxes_control_panel__button">
                <v-tooltip bottom>

                    <template v-slot:activator="{ on }">
                        <v-btn v-on="on" flat icon color="red" @click.stop="destroyEntries">
                            <v-icon>delete</v-icon>
                        </v-btn>
                    </template>

                    <span>Удалить</span>
                </v-tooltip>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: 'ControlPanelFaxes',
        props: {
            destroy: {
                type: Boolean,
                default: false,
            },
            edit: {
                type: Boolean,
                default: false,
            },
            expandProps: {
                type: Object,
                default: () => ({}),
            },
            download: {
                type: Boolean,
                default: false,
            },
            selected: {
                type: Array,
                default: () => [],
            },
            item: {
                type: Object,
                default: () => ({}),
            },
        },
        methods: {
            openCloseFaxesExpandPanel () {
                _.set(this.expandProps, 'expanded', !this.expandProps.expanded);
                _.set(this.expandProps, 'selected', !this.expandProps.selected);
            },
            downloadExcle () {
                // axios.get('fax/download');
                axios({
                    url: 'fax/download',
                    method: 'GET',
                    responseType: 'blob', // important
                }).then((response) => {
                    if (!window.navigator.msSaveOrOpenBlob) {
                        // BLOB NAVIGATOR
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'more.xlsx');
                        document.body.appendChild(link);
                        link.click();
                    } else {
                        // BLOB FOR EXPLORER 11
                        window.navigator.msSaveOrOpenBlob(new Blob([response.data]), 'more.xlsx');
                    }
                });
            },
            destroyEntries () {
                console.log('DES', this.item);
                console.log('selected', this.selected);
                let faxNames = [];
                let itemsID = [];
                if (!_.isEmpty(this.selected)) {
                    itemsID = _.map(this.selected, item => item.id);
                    faxNames = _.map(this.selected, item => item.fax_name);
                } else if (!_.isEmpty(this.item)) {
                    itemsID.push(this.item.id);
                    faxNames.push(this.item.fax_name);
                }

                this.$snotify.confirm(`Удалить факсы - "${faxNames}"`, {
                    timeout: 5000,
                    showProgressBar: true,
                    closeOnClick: false,
                    pauseOnHover: true,
                    buttons: [
                        {
                            text: 'Yes', action: (toast) => {
                                this.$snotify.remove(toast.id);
                                this.destroyFaxEntries(itemsID, faxNames);
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
            async destroyFaxEntries (faxesIDS, faxNames) {
                try {
                    const { data } = await axios.post('faxes/delete', { faxesIDS });
                    const { status } = data;
                    console.log('faxesIDS', faxesIDS);

                    if (status) {
                        this.$store.dispatch('fax/destroyFaxes', faxesIDS);

                        this.$snotify.success(`Факсы < ${faxNames} > успешно удалены.`, {
                            timeout: 3000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true,
                        });
                    }
                } catch (error) {
                    console.error(`Ошибка при удалении факсов - ${error}`);
                } finally {
                    console.log('Request delete faxes completed.');
                }
            },
        },
    };
</script>

<style lang="scss">
    .faxes_control_panel {
        background-color: #ebeef0;

        .faxes_control_panel__button_wraper {
            display: flex;
            justify-content: space-around;
        }
    }

</style>
