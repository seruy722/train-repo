<template>
    <div
        data-vue-component-name="StartPage"
        class="main"
    >
        <template>
            <v-item-group>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex
                            v-for="(card, i) in cards"
                            :key="i"
                            xs6
                            md2
                            sm2
                        >
                            <v-item>
                                <v-hover>
                                    <v-card
                                        v-ripple
                                        slot-scope="{ hover }"
                                        color="primary"
                                        :class="`elevation-${hover ? 12 : 2}`"
                                        class="d-flex align-center justify-center mx-auto"
                                        height="100px"
                                        max-width="100px"
                                    >
                                        <v-scroll-y-transition>
                                            <div
                                                class="text-xs-center"
                                                @click="navClick(card)"
                                            >
                                                {{ card.text }}
                                            </div>
                                        </v-scroll-y-transition>
                                    </v-card>
                                </v-hover>
                            </v-item>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-item-group>
        </template>
    </div>
</template>

<script>
    import navClickMixin from '~/mixins/navClick';
    import logout from '~/mixins/logout';
    import { mapGetters } from 'vuex';

    export default {
        name: 'StartPage',
        mixins: [navClickMixin, logout],
        data: () => ({
            cards: [],
        }),
        computed: {
            ...mapGetters({
                menuMain: 'settings/menuMain',
                role: 'auth/role',
            }),
        },
        created () {
            this.getMenu(this.menuMain, this.role);
        },
        methods: {
            async getMenu (menu, userRole) {
                _.forEach(menu, (item) => {
                    const { path, children, role } = item;
                    if (path && path !== '/' && _.includes(role, userRole)) {
                        this.cards.push(item);
                    } else if (children) {
                        // console.log(userRole);
                        _.forEach(children, (child) => {
                            const { path, role } = child;
                            if (path && path !== '/' && _.includes(role, userRole)) {
                                this.cards.push(child);
                            }
                        });
                    }
                });
            },
        },
    };
</script>
