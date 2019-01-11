<template>
    <v-menu
        ref="menu1"
        v-model="menu"
        :close-on-content-click="false"
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
            persistent-hint
            prepend-icon="event"
            @blur="date = parseDate(dateFormatted)"
        ></v-text-field>

        <v-date-picker
            v-model="date"
            first-day-of-week="1"
            locale="ru-ru"
            no-title
            @input="menu = false"
        ></v-date-picker>
    </v-menu>
</template>

<script>
    export default {
        name: 'DatePicker',
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
                    return this.value || null;
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

                const [year, month, day] = date.split('-');
                return `${day}-${month}-${year}`;
            },
            parseDate (date) {
                if (!date) return null;

                const [day, month, year] = date.split('-');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            },
        },
    };
</script>
