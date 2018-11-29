<template>
    <v-menu
        ref="menu"
        :close-on-content-click="false"
        v-model="menu"
        :nudge-right="40"
        lazy
        transition="scale-transition"
        offset-y
        full-width
        min-width="290px"
    >
        <v-text-field
            slot="activator"
            v-model="date"
            label="Год"
            prepend-icon="event"
            readonly
        ></v-text-field>
        <v-date-picker
            ref="picker"
            v-model="date"
            :max="new Date().toISOString().substr(0, 10)"
            @input="save"
            reactive
            no-title
        ></v-date-picker>
    </v-menu>
</template>

<script>
    export default {
        data: () => ({
            date: null,
            menu: false
        }),
        watch: {
            menu (val) {
                val && this.$nextTick(() => (this.$refs.picker.activePicker = 'MONTH'));
            }
        },
        methods: {
            save (date) {
                this.$refs.menu.save(date);
                this.$refs.picker.activePicker = 'MONTH';
                this.menu = false;
            }
        }
    }
</script>
