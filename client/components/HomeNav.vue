<template>
    <v-menu offset-y>
        <span data-component-name="HomeNav"></span>
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
</template>

<script>
    import {mapGetters} from 'vuex';
    import navClickMixin from '~/mixins/navClick';
    import logout from '~/mixins/logout';

    export default {
        mixins: [navClickMixin, logout],
        computed: {
            ...mapGetters({
                user: 'auth/user'
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
        }
    }
</script>
