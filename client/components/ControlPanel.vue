<template>
    <div data-vue-component="ControlPanel">
        <v-container fluid>
            <v-layout row wrap justify-space-around>

                <v-flex xs12 sm6 md6>
                    <DateCalendar></DateCalendar>
                </v-flex>

                <v-flex xs12 sm3 md3>
                    <v-combobox
                        v-model="client"
                        :items="clients"
                        :error-messages="checkError('client')"
                        prepend-icon="people"
                        label="Клиент"
                        item-text="name"
                        item-value="id"
                        hide-selected
                        return-object
                    ></v-combobox>
                </v-flex>

                <v-flex xs12 sm2 md2>
                    <v-select
                        v-model="currentTable"
                        :items="items"
                        label="Таблица"
                    ></v-select>
                </v-flex>

            </v-layout>
        </v-container>
    </div>
</template>

<script>
    import DateCalendar from '~/components/DateCalendar.vue';
    import { mapGetters } from 'vuex';
    import { isClient } from '~/utils';
    import checkErrorMixin from '~/mixins/checkError';

    export default {
        name: 'ControlPanel',
        components: {
            DateCalendar,
        },
        mixins: [checkErrorMixin],
        data: () => ({
            items: ['КАРГО', 'ДОЛГИ'],
            client: 'Все',
        }),
        computed: {
            ...mapGetters({
                clients: 'cargo/clientsNames',
                table: 'cargo/getCurrentTable',
            }),
            currentTable: {
                get: function () {
                    return this.table;
                },
                set: function (tableName) {
                    this.$store.commit('cargo/SET_TABLE', tableName);
                    this.$store.dispatch('cargo/changeList');
                },
            },
        },
        watch: {
            async client (clientObj) {
                console.log('clientObj', clientObj);
                this.changeErrors({});
                if (isClient(this.clients, clientObj)) {
                    this.$store.commit('cargo/SET_CLIENT', clientObj);
                    await this.$store.dispatch('cargo/changeList');
                } else {
                    this.$snotify.warning(`Клиента '${clientObj}' нет в базе`, {
                        timeout: 3000,
                        showProgressBar: true,
                        closeOnClick: true,
                        pauseOnHover: true,
                    });
                    this.changeErrors({
                        client: `Клиента '${clientObj}' нет в базе`,
                    });
                }
            },
        },
    };
</script>
