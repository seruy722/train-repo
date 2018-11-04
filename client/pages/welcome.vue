<template>
    <div>
        <toolbar
        >

        </toolbar>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import Toolbar from '~/components/Toolbar';

    export default {
        middleware: 'auth',
        components: {Toolbar},
        // layout: 'simple',

        head () {
            return {title: this.$t('home')}
        },

        data: () => ({
            title: process.env.appName
        }),
    }
</script>

<style scoped>
    .top-right {
        position: absolute;
        right: 10px;
        top: 18px;
    }

    .title {
        font-size: 85px;
    }
</style>
