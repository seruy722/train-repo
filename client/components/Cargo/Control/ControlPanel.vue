<template>
    <div data-vue-component="ControlPanel">
    <v-container fluid>
        <v-layout row wrap justify-space-around>

            <v-flex xs12 sm4 md4>
                <DateCalendar></DateCalendar>
            </v-flex>

            <v-flex xs12 sm3 md3>
                <v-combobox
                    v-model="client"
                    :items="clients"
                    prepend-icon="people"
                    label="Клиент"
                    hide-selected
                    @keyup="keydwn"
                ></v-combobox>
            </v-flex>

            <v-flex xs12 sm2 md2>
                <v-select
                    :items="items"
                    v-model="currentTable"
                    label="Таблица"
                    @change="changeTable"
                ></v-select>
            </v-flex>

        </v-layout>
    </v-container>
    </div>
</template>

<script>
    import DateCalendar from '~/components/Cargo/Control/DatePicker/DateCalendar';
    import {mapGetters} from 'vuex';

    export default {
        name: 'ControlPanel',
        components: {
            DateCalendar,
        },
        data: () => ({
            currentTable: null,
            items: ['КАРГО', 'ДОЛГИ'],
            client: '',
            arr: []
        }),
        created () {
            this.currentTable = this.$store.getters['cargo/getCurrentTable'];
        },
        watch: {
            client (val) {
                this.$store.commit('cargo/SET_CLIENT', val);
                this.$store.commit('cargo/CHANGE_CARGOLIST');
                this.$store.dispatch('cargo/calcData');
            }
        },
        computed: {
            ...mapGetters({
                clients: 'cargo/clientsNames',
                isClientName: 'controlPanel/getisClientName'
            })
        },
        methods: {
            keydwn (e) {
                // e.target.value = 'serg';
                console.log('INP', e.keyCode);
                // const value = e.target.value;
                //
                // const rr = _.some(this.clients, (item) => {
                //     return item.indexOf(value) !== -1;
                // });
                //
                // if (!rr) {
                //     e.preventDefault();
                //     e.target.value = value.slice(0,-1);
                // }
            },
            changeTable (title) {
                this.$store.commit('cargo/SET_TABLE', title);
                this.$store.commit('cargo/CHANGE_CARGOLIST');
                this.$store.dispatch('cargo/calcData');
            }
        }
    }
</script>
