<template>
    <div
        data-vue-component-name="TimePicker"
    >
        <v-dialog
            ref="dialog"
            v-model="modal2"
            :return-value.sync="time"
            persistent
            lazy
            full-width
            width="290px"
        >
            <template v-slot:activator="{ on }">
                <v-text-field
                    v-model="time"
                    label="Время"
                    prepend-icon="access_time"
                    readonly
                    clearable
                    v-on="on"
                ></v-text-field>
            </template>
            <v-time-picker
                v-if="modal2"
                v-model="time"
                full-width
                format="24hr"
            >
                <v-spacer></v-spacer>
                <v-btn flat color="primary" @click="modal2 = false">Cancel</v-btn>
                <v-btn flat color="primary" @click="$refs.dialog.save(time)">OK</v-btn>
            </v-time-picker>
        </v-dialog>
    </div>
</template>

<script>
    export default {
        name: 'TimePicker',
        props: {
            value: {
                type: String,
                required: true,
            },
        },
        data () {
            return {
                e7: null,
                // time: null,
                modal2: false,
            };
        },
        computed: {
            time: {
                get () {
                    return this.value;
                },
                set (val) {
                    this.$emit('update:value', val);
                },
            },
        },
    };
</script>
