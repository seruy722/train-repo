<template>
    <v-container fluid>
        <v-layout row wrap justify-space-around>

            <v-flex xs12 sm4 md4>
                <date-calendar></date-calendar>
            </v-flex>

            <v-flex xs12 sm2 md2>
                <!--ПОИСК-->
                <search :value.sync="search"></search>
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
</template>

<script>
    import DateCalendar from '~/components/Cargo/Control/DatePicker/DateCalendar';
    import Search from '~/components/Search';

    export default {
        components: {
            DateCalendar,
            Search
        },
        data: () => ({
            search: '',
            currentTable: null,
            items: ['КАРГО', 'ДОЛГИ']
        }),
        watch: {
            search (val) {
                this.$store.commit('controlPanel/SET_SEARCH', val);
            }
        },
        created () {
            this.currentTable = this.$store.getters['controlPanel/getCurrentTable'];
        },
        methods: {
            changeTable (title) {
                this.$store.commit('controlPanel/SET_TABLE', title);
            }
        }
    }
</script>
