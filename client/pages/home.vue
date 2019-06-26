<template>
    <div data-component-name="Home">

        <v-app id="inspire">
            <v-navigation-drawer
                v-if="access"
                :clipped="$vuetify.breakpoint.lgAndUp"
                v-model="drawer"
                fixed
                app
            >
                <v-toolbar flat class="transparent">
                    <v-list class="pa-0">
                        <v-list-tile avatar>
                            <v-list-tile-avatar>
                                <img :src="`${profileImage ? imageUrl + profileImage : defaultFoto}`">
                            </v-list-tile-avatar>

                            <v-list-tile-content>
                                <v-list-tile-title>{{ user.name || '' }}</v-list-tile-title>
                            </v-list-tile-content>

                            <v-list-tile-action>
                                <v-btn
                                    icon
                                    @click.stop="drawer = !drawer"
                                >
                                    <v-icon>chevron_left</v-icon>
                                </v-btn>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </v-toolbar>

                <!--<v-list dense>-->
                <!--<template v-for="item in items">-->
                <!--<v-layout-->
                <!--v-if="item.heading"-->
                <!--:key="item.heading"-->
                <!--row-->
                <!--align-center-->
                <!--&gt;-->
                <!--<v-flex xs6>-->
                <!--<v-subheader v-if="item.heading">-->
                <!--{{ item.heading }}-->
                <!--</v-subheader>-->
                <!--</v-flex>-->
                <!--<v-flex xs6 class="text-xs-center">-->
                <!--<a href="#!" class="body-2 black&#45;&#45;text">EDIT</a>-->
                <!--</v-flex>-->
                <!--</v-layout>-->
                <!--<v-list-group-->
                <!--v-else-if="item.children"-->
                <!--v-model="item.model"-->
                <!--:key="item.text"-->
                <!--:prepend-icon="item.icon"-->
                <!--&gt;-->
                <!--<v-list-tile slot="activator">-->
                <!--<v-list-tile-content>-->
                <!--<v-list-tile-title>-->
                <!--{{ item.text }}-->
                <!--</v-list-tile-title>-->
                <!--</v-list-tile-content>-->
                <!--</v-list-tile>-->
                <!--<v-list-tile-->
                <!--v-for="(child, i) in item.children"-->
                <!--:key="i"-->
                <!--@click="navClick(child)"-->
                <!--class="ml-4"-->
                <!--&gt;-->
                <!--<v-list-tile-action v-if="child.icon">-->
                <!--<v-icon>{{ child.icon }}</v-icon>-->
                <!--</v-list-tile-action>-->
                <!--<v-list-tile-content>-->
                <!--<v-list-tile-title>-->
                <!--{{ child.text }}-->
                <!--</v-list-tile-title>-->
                <!--</v-list-tile-content>-->
                <!--</v-list-tile>-->
                <!--</v-list-group>-->
                <!--<v-list-tile v-else :key="item.text" @click="navClick(item)">-->
                <!--<v-list-tile-action>-->
                <!--<v-icon>{{ item.icon }}</v-icon>-->
                <!--</v-list-tile-action>-->
                <!--<v-list-tile-content>-->
                <!--<v-list-tile-title>-->
                <!--{{ item.text }}-->
                <!--</v-list-tile-title>-->
                <!--</v-list-tile-content>-->
                <!--</v-list-tile>-->
                <!--</template>-->
                <!--</v-list>-->

                <v-list>
                    <div
                        v-for="(item, i) in $_items"
                        :key="i"
                    >
                        <v-list-tile
                            v-if="item.path && showHideMenu(item)"
                            @click="navClick(item)"
                        >
                            <v-list-tile-action>
                                <v-icon v-text="item.icon"></v-icon>
                            </v-list-tile-action>
                            <v-list-tile-title v-text="item.text"></v-list-tile-title>
                        </v-list-tile>

                        <v-list-group
                            v-else-if="!item.path && showHideMenu(item)"
                            v-model="item.model"
                            no-action
                            :prepend-icon="item.icon"
                            value="true"
                        >
                            <template v-slot:activator>
                                <v-list-tile>
                                    <v-list-tile-title v-text="item.text"></v-list-tile-title>
                                </v-list-tile>
                            </template>
                            <div
                                v-for="(child, index) in item.children"
                                :key="index"
                            >
                                <v-list-tile
                                   v-if="showHideMenu(child)"
                                    @click="navClick(child)"
                                >
                                    <v-list-tile-action>
                                        <v-icon v-text="child.icon"></v-icon>
                                    </v-list-tile-action>
                                    <v-list-tile-title v-text="child.text"></v-list-tile-title>
                                </v-list-tile>
                            </div>

                        </v-list-group>
                    </div>
                    <!--<v-list-group-->
                    <!--prepend-icon="account_circle"-->
                    <!--value="true"-->
                    <!--&gt;-->
                    <!--<template v-slot:activator>-->
                    <!--<v-list-tile>-->
                    <!--<v-list-tile-title>Users</v-list-tile-title>-->
                    <!--</v-list-tile>-->
                    <!--</template>-->
                    <!--<v-list-group-->
                    <!--no-action-->
                    <!--sub-group-->
                    <!--value="true"-->
                    <!--&gt;-->
                    <!--<template v-slot:activator>-->
                    <!--<v-list-tile>-->
                    <!--<v-list-tile-title>Admin</v-list-tile-title>-->
                    <!--</v-list-tile>-->
                    <!--</template>-->

                    <!--<v-list-tile-->
                    <!--v-for="(item, i) in items"-->
                    <!--:key="i"-->
                    <!--@click=""-->
                    <!--&gt;-->
                    <!--<v-list-tile-title v-text="item.text"></v-list-tile-title>-->
                    <!--<v-list-tile-action>-->
                    <!--<v-icon v-text="item.icon"></v-icon>-->
                    <!--</v-list-tile-action>-->
                    <!--</v-list-tile>-->
                    <!--</v-list-group>-->

                    <!--&lt;!&ndash;<v-list-group&ndash;&gt;-->
                    <!--&lt;!&ndash;sub-group&ndash;&gt;-->
                    <!--&lt;!&ndash;no-action&ndash;&gt;-->
                    <!--&lt;!&ndash;&gt;&ndash;&gt;-->
                    <!--&lt;!&ndash;<template v-slot:activator>&ndash;&gt;-->
                    <!--&lt;!&ndash;<v-list-tile>&ndash;&gt;-->
                    <!--&lt;!&ndash;<v-list-tile-title>Actions</v-list-tile-title>&ndash;&gt;-->
                    <!--&lt;!&ndash;</v-list-tile>&ndash;&gt;-->
                    <!--&lt;!&ndash;</template>&ndash;&gt;-->
                    <!--&lt;!&ndash;<v-list-tile&ndash;&gt;-->
                    <!--&lt;!&ndash;v-for="(crud, i) in cruds"&ndash;&gt;-->
                    <!--&lt;!&ndash;:key="i"&ndash;&gt;-->
                    <!--&lt;!&ndash;@click=""&ndash;&gt;-->
                    <!--&lt;!&ndash;&gt;&ndash;&gt;-->
                    <!--&lt;!&ndash;<v-list-tile-title v-text="crud[0]"></v-list-tile-title>&ndash;&gt;-->
                    <!--&lt;!&ndash;<v-list-tile-action>&ndash;&gt;-->
                    <!--&lt;!&ndash;<v-icon v-text="crud[1]"></v-icon>&ndash;&gt;-->
                    <!--&lt;!&ndash;</v-list-tile-action>&ndash;&gt;-->
                    <!--&lt;!&ndash;</v-list-tile>&ndash;&gt;-->
                    <!--&lt;!&ndash;</v-list-group>&ndash;&gt;-->
                    <!--</v-list-group>-->
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
                        v-if="access"
                        @click.stop="drawer = !drawer"
                    ></v-toolbar-side-icon>
                    <span class="hidden-sm-and-down">
                        <v-icon
                            medium
                            color="green"
                        >
                            {{ pageSettings.icon }}
                        </v-icon>
                    </span>
                    <span class="hidden-sm-and-down">{{ pageSettings.title }}</span>
                </v-toolbar-title>
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
    import navClickMixin from '~/mixins/navClick';
    import logout from '~/mixins/logout';

    export default {
        name: 'Home',
        mixins: [navClickMixin, logout],
        middleware: 'auth',
        access: false,
        data () {
            this.$_items = [];
            return {
                drawer: null,
            };
        },
        computed: {
            ...mapGetters({
                user: 'auth/user',
                profileImage: 'auth/userProfileImg',
                defaultFoto: 'settings/defaultFoto',
                imageUrl: 'settings/imageUrl',
                pageSettings: 'settings/pageSettings',
                menuMain: 'settings/menuMain',
            }),
        },
        created () {
            this.checkRole();
            this.$store.dispatch('settings/setPageSettings', { title: 'Главная', icon: 'home' });
            this.$_items = _.cloneDeep(this.menuMain);
            // console.log('USER', this.user);
        },
        methods: {
            checkRole () {
                if (this.user.role !== 'user') {
                    this.access = true;
                }
            },
            showHideMenu (item) {
                const { children = [] } = item;
                if (!_.isEmpty(children)) {
                    return _.some(children, elem => _.includes(elem.role, this.user.role));
                    // if (!sm) {
                    //     return false;
                    // }
                }
                return _.includes(item.role, this.user.role);
            },
        },
    };
</script>
