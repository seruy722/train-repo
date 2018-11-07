<template>
    <div data-component-name="Home">
        <v-app id="inspire">
            <v-toolbar
                :clipped-left="$vuetify.breakpoint.lgAndUp"
                color="main_color-bg darken-3"
                dark
                app
                fixed
            >
                <v-toolbar-title style="width: 320px" class="ml-0 pl-3">
                    <span class="mr-2 hidden-sm-and-down"><v-icon medium color="black">people</v-icon></span>
                    <span class="hidden-sm-and-down">Черный список клиентов</span>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-avatar>
                    <v-img
                        :src="profileImage"
                        aspect-ratio="1"
                        alt="Avatar"
                        class="grey lighten-2"
                    >
                        <v-layout
                            slot="placeholder"
                            fill-height
                            align-center
                            justify-center
                            ma-0
                        >
                            <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                        </v-layout>
                    </v-img>
                </v-avatar>
                <!--МЕНЮ-->
                <home-nav></home-nav>
            </v-toolbar>
            <v-content>
                <v-container fluid fill-height>
                    <v-layout justify-center align-center>
                        <nuxt-child :key="$route.name"/>
                    </v-layout>
                </v-container>
            </v-content>
            <!--Компонент оповещений-->
            <vue-snotify></vue-snotify>
        </v-app>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import HomeNav from '~/components/HomeNav';

    export default {
        components:{
            HomeNav // Меню навигации
        },
        name: 'MyComponent',
        middleware: 'auth',
        computed: {
            ...mapGetters({
                profileImage: 'auth/userProfileImg',
            })
        }
    }
</script>

<style lang="scss">
    @import "~vue-snotify/styles/material"; // Стили для оповещений
</style>
