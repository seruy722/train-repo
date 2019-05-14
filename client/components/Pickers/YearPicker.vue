<template>
    <div data-component-name="YearPicker">
        <v-menu
            ref="menu"
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            lazy
            transition="scale-transition"
            offset-y
            full-width
            min-width="290px"
        >
            <v-text-field
                slot="activator"
                v-model="dateFormatted"
                label="Год"
                prepend-icon="event"
                readonly
            ></v-text-field>

            <v-date-picker
                ref="picker"
                v-model="date"
                :max="new Date().toISOString().substr(0, 10)"
                reactive
                no-title
                @input="save"
            ></v-date-picker>
        </v-menu>
    </div>
</template>

<script>
    export default {
        name: 'YearPicker',
        props: {
            value: {
                type: String,
                default: new Date().toISOString().substr(0, 4),
            },
        },
        data: () => ({
            date: null,
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
            menu () {
                this.$nextTick(() => {
                    this.$refs.picker.activePicker = 'YEAR';
                });
            },
            date () {
                const [year] = this.date.split('-');
                this.dateFormatted = year;
            },
        },
        methods: {
            save (date) {
                this.$refs.menu.save(date);
                this.$refs.picker.activePicker = 'YEAR';
                this.menu = false;
            },
        },
    };
</script>
