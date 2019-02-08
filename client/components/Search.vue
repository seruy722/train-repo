<template>
    <v-text-field
        ref="search"
        v-model="search"
        append-icon="search"
        label="Поиск"
        single-line
        hide-details
    ></v-text-field>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                default: '',
            },
        },
        computed: {
            search: {
                get () {
                    return this.value;
                },
                set (val) {
                    this.debouncedSearch(val);
                },
            },
        },
        methods: {
            debouncedSearch: _.debounce(function (val) {
                this.$emit('update:value', val);
            }, 500),
        },
    };
</script>
