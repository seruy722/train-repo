import Vue from 'vue';
import Router from 'vue-router';
import {scrollBehavior} from '~/utils';

Vue.use(Router);

const Home = () => import('~/pages/home').then(m => m.default || m);
const BlackList = () => import('~/pages/home/blacklist').then(m => m.default || m);
const Logs = () => import('~/pages/home/logs').then(m => m.default || m);
const Profile = () => import('~/pages/home/profile').then(m => m.default || m);

const Login = () => import('~/pages/auth/login').then(m => m.default || m);
const Register = () => import('~/pages/auth/register').then(m => m.default || m);


const routes = [
    {path: '/', component: Home,
        children: [
            {
                path: '/',
                component: BlackList,
                name: 'home-blacklist'
            },
            {
                path: 'profile',
                component: Profile,
                name: 'home-profile',
            },
            {
                path: 'logs',
                component: Logs,
                name: 'home-logs',
            },
        ]
    },

    {path: '/login', name: 'login', component: Login},
    {path: '/register', name: 'register', component: Register},

];

export function createRouter () {
    return new Router({
        routes,
        scrollBehavior,
        mode: 'history',
    })
}
