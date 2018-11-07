<template>
    <v-text-field
        append-icon="search"
        label="Поиск"
        single-line
        hide-details
        ref="search"
        v-model="search"
    ></v-text-field>
</template>

<script>
    export default {
        props:['value'],
        computed: {
            search: {
                get () {
                    return this.value;
                },
                set (val) {
                    this.$emit('update:value', val);
                }
            }
        }
    }
</script>
