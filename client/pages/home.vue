<template>
    <div data-component-name="Home">
        <v-app id="inspire">
            <v-navigation-drawer
                v-if="showAdminData"
                :clipped="$vuetify.breakpoint.lgAndUp"
                v-model="drawer"
                fixed
                app
            >
                <v-list dense>
                    <template v-for="item in items">
                        <v-layout
                            v-if="item.heading"
                            :key="item.heading"
                            row
                            align-center
                        >
                            <v-flex xs6>
                                <v-subheader v-if="item.heading">
                                    {{ item.heading }}
                                </v-subheader>
                            </v-flex>
                            <v-flex xs6 class="text-xs-center">
                                <a href="#!" class="body-2 black--text">EDIT</a>
                            </v-flex>
                        </v-layout>
                        <v-list-group
                            v-else-if="item.children"
                            v-model="item.model"
                            :key="item.text"
                            :prepend-icon="item.icon"
                        >
                            <v-list-tile slot="activator">
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        {{ item.text }}
                                    </v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-list-tile
                                v-for="(child, i) in item.children"
                                :key="i"
                                @click="navClick(child)"
                                class="ml-4"
                            >
                                <v-list-tile-action v-if="child.icon">
                                    <v-icon>{{ child.icon }}</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        {{ child.text }}
                                    </v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list-group>
                        <v-list-tile v-else :key="item.text" @click="navClick(item)">
                            <v-list-tile-action>
                                <v-icon>{{ item.icon }}</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>
                                    {{ item.text }}
                                </v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </template>
                </v-list>
            </v-navigation-drawer>
            <v-toolbar
                :clipped-left="$vuetify.breakpoint.lgAndUp"
                color="main_color-bg darken-3"
                dark
                app
                fixed
            >
                <v-toolbar-title style="width: 500px" class="ml-0 pl-3">
                    <v-toolbar-side-icon
                        v-if="showAdminData"
                        @click.stop="drawer = !drawer"
                    ></v-toolbar-side-icon>
                    <span class="mr-2 ml-2 hidden-sm-and-down"><v-icon medium color="black">people</v-icon></span>
                    <span class="hidden-sm-and-down">Черный список клиентов</span>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-avatar>
                    <v-img
                        :src="`${profileImage ? imageUrl + profileImage : defaultFoto}`"
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
    import { mapGetters } from 'vuex';
    import HomeNav from '~/components/Navs/HomeNav';
    import navClickMixin from '~/mixins/navClick';
    import logout from '~/mixins/logout';

    export default {
        mixins: [navClickMixin, logout],
        components: {
            HomeNav // Меню навигации
        },
        middleware: 'auth',
        showAdminData: false,
        data: () => ({
            drawer: false,
            items: [
                { icon: 'home', text: 'Главная', path: '/' },
                { icon: 'people_outline', text: 'Пользователи', path: '/users' },
                {
                    icon: 'business',
                    text: 'Карго Долги',
                    model: false,
                    children: [
                        { icon: 'home', text: 'Карго', path: '/cargo' },
                        { icon: 'home', text: 'Факсы', path: '/faxes' },
                    ],
                },
                {
                    icon: 'people',
                    text: 'Чорный список',
                    model: false,
                    children: [
                        { icon: 'home', text: 'Главная', path: '/' },
                        { icon: 'list', text: 'Логи', path: '/logs' },
                    ],
                },
                { icon: 'email', text: 'Email доступы', path: '/emails' },
                { icon: 'exit_to_app', text: 'Выход', path: '/logout' },
            ],
        }),
        created () {
            this.checkAdminRole();
        },
        computed: {
            ...mapGetters({
                profileImage: 'auth/userProfileImg',
                defaultFoto: 'settings/defaultFoto',
                imageUrl: 'settings/imageUrl',
                role: 'auth/role',
                adminRole: 'settings/adminRole'
            })
        },
        methods: {
            checkAdminRole () {
                if (this.role === this.adminRole) {
                    this.showAdminData = true;
                }
            }
        }
    }
</script>
