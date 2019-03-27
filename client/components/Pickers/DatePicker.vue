<template>
    <div data-vue-component-name="DatePicker">
        <v-menu
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
            <template v-slot:activator="{ on }">
                <v-text-field
                    v-model="dateFormatted"
                    :label="datePickerSettings.label || 'Дата'"
                    :error-messages="checkError('pickerDate')"
                    persistent-hint
                    prepend-icon="event"
                    readonly
                    clearable
                    v-on="on"
                ></v-text-field>
            </template>

            <v-date-picker
                v-model="date"
                first-day-of-week="1"
                locale="ru-ru"
                no-title
                @input="menu = false"
            ></v-date-picker>
        </v-menu>
    </div>
</template>

<script>
    import checkErrorMixin from '~/mixins/checkError';
    import { needFormatDate } from '~/utils/dates';

    export default {
        name: 'DatePicker',
        mixins: [checkErrorMixin],
        props: {
            value: {
                type: String,
                default: '',
            },
            datePickerSettings: {
                type: Object,
                default: () => null,
            },
            errorDate: {
                type: String,
                default: '',
            },
        },
        data: () => ({
            // date: needFormatDate(new Date().toISOString()),
            date: new Date().toISOString().substr(0, 10),
            menu: false,
        }),
        computed: {
            dateFormatted: {
                get () {
                    return needFormatDate(this.value);
                },
                set (val) {
                    this.$emit('update:value', val);
                },
            },
        },

        watch: {
            date () {
                // console.log('DATE', this.dateFormatted);
                this.dateFormatted = needFormatDate(this.date);
            },
            errorDate (error) {
                this.changeErrors({ pickerDate: error });
            },
        },
        created () {
            if (!this.value) {
                this.dateFormatted = this.date;
            }
        },
    };
</script>
