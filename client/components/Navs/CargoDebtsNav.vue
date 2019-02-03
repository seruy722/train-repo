<template>
    <div data-vue-component="CargoDebtsNav">
        <v-menu
            bottom
            origin="center center"
            transition="scale-transition"
        >
            <v-btn
                slot="activator"
                color="primary"
                dark
            >
                Новый
            </v-btn>

            <v-list>
                <v-list-tile
                    v-for="(item, i) in menuElem"
                    :key="i"
                    @click="clickNav(item.title)"
                >
                    <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                </v-list-tile>
            </v-list>
        </v-menu>
    </div>
</template>

<script>
    export default {
        name: 'CargoDebtsNav',
        data: () => ({
            menuElem: [
                { title: 'ОПЛАТА' },
                { title: 'ДОЛГ' },
            ],
        }),
        methods: {
            clickNav (title) {
                this.$emit('click-nav', title);
            },
        },
    };
</script>
