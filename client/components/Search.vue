<template>
    <div data-vue-component-name="Search">
        <v-text-field
            v-model="search"
            append-icon="search"
            label="Поиск"
            single-line
            hide-details
        ></v-text-field>
    </div>
</template>

<script>
    export default {
        name: 'Search',
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
