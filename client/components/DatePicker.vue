<template>
    <v-container grid-list-md>
        <v-layout row wrap>
            <v-flex xs12 lg6>
                <v-menu
                    ref="menu1"
                    :close-on-content-click="false"
                    v-model="menu"
                    :nudge-right="40"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    max-width="290px"
                    min-width="290px"
                >
                    <v-text-field
                        slot="activator"
                        v-model="dateFormatted"
                        label="Дата"
                        hint="MM-DD-YYYY format"
                        persistent-hint
                        prepend-icon="event"
                        @blur="date = parseDate(dateFormatted)"
                    ></v-text-field>
                    <v-date-picker v-model="date" no-title @input="menu = false"></v-date-picker>
                </v-menu>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                default: '',
            },
        },

        data: () => ({
            date: new Date().toISOString().substr(0, 10),
            menu: false,
        }),

        computed: {
            dateFormatted: {
                get () {
                    return this.value || this.formatDate(new Date().toISOString().substr(0, 10));
                },
                set (val) {
                    this.$emit('update:value', val);
                },
            },
        },

        watch: {
            date () {
                this.dateFormatted = this.formatDate(this.date);
            },
        },

        methods: {
            formatDate (date) {
                if (!date) return null;
                console.log('Ddate', date);
                const [year, month, day] = date.split('-');
                return `${day}-${month}-${year}`;
            },
            parseDate (date) {
                if (!date) return null;
                console.log('Ddate2', date);
                const [day, month, year] = date.split('-');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            },
        },
    };
</script>
