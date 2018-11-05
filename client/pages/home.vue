<template>
    <div data-component-name="Home">
        <v-app id="inspire">
            <v-toolbar
                :clipped-left="$vuetify.breakpoint.lgAndUp"
                color="blue darken-3"
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
                <v-menu offset-y>
                    <v-btn flat slot="activator">
                        {{user.name}}
                    </v-btn>

                    <v-list>
                        <v-list-tile
                            v-for="item in role"
                            :key="item.path"
                            @click="navClick(item)"
                        >
                            <v-icon class="mr-2">{{ item.icon }}</v-icon>
                            {{ item.title }}
                        </v-list-tile>
                    </v-list>
                </v-menu>
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

    export default {
        name: 'MyComponent',
        middleware: 'auth',
        data: () => ({
            drawer: false,
        }),
        computed: {
            ...mapGetters({
                user: 'auth/user',
                profileImage: 'auth/userProfileImg',
                profileNav: 'nav/profileNav',
            }),
            role () {
                const userRole = this.$store.getters['auth/role'];
                const nav = this.$store.getters['nav/profileNav'];
                const newArr = [];
                _.forEach(nav, (item) => {
                    if (_.includes(item.role, userRole)) {
                        newArr.push(item);
                    }
                });
                return newArr;
            }
        },
        methods: {
            navClick (item) {
                if (item.path === '/logout') {
                    this.logout();
                }
                this.$router.push(item.path);
            },
            async logout () {
                // Log out the user.
                await this.$store.dispatch('auth/logout');

                // Redirect to login.
                this.$router.push('/');
            }
        }
    }
</script>

<style lang="scss">
    @import "~vue-snotify/styles/material"; // Стили для оповещений
</style>
