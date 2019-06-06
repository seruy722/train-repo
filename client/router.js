import Vue from 'vue';
import Router from 'vue-router';
import { scrollBehavior } from '~/utils';

Vue.use(Router);
// // Main page
// const Home = () => import('~/pages/home').then(m => m.default || m);
// // BlackList users
// const BlackList = () => import('~/pages/blacklist/blacklist').then(m => m.default || m);
// // Logs for blacklist users
// const Logs = () => import('~/pages/blacklist/logs').then(m => m.default || m);
// // Profile users
// const Profile = () => import('~/pages/users/profile').then(m => m.default || m);
// // Auth
// const Login = () => import('~/pages/auth/login').then(m => m.default || m);
// const Register = () => import('~/pages/auth/register').then(m => m.default || m);
// // Edit users data
// const Users = () => import('~/pages/users/users').then(m => m.default || m);
// // Emails for access to site
// const Emails = () => import('~/pages/accessemails/emails').then(m => m.default || m);
//
// // Cargo && Debts
// const Cargo = () => import('~/pages/cargo/cargo').then(m => m.default || m);
//
// // // Faxes
// // const Faxes = () => import('~/pages/faxes').then(m => m.default || m);

const routes = [
    {
        path: '/',
        component: () => import('~/pages/home').then(m => m.default || m),
        children: [
            {
                path: '/',
                component: () => import('~/pages/StartPage').then(m => m.default || m),
                name: 'home-blacklist',
            },
            {
                path: 'profile',
                component: () => import('~/pages/users/profile').then(m => m.default || m),
                name: 'home-profile',
            },
            {
                path: 'logs',
                component: () => import('~/pages/blacklist/logs').then(m => m.default || m),
                name: 'home-logs',
            },
            {
                path: 'users',
                component: () => import('~/pages/users/users').then(m => m.default || m),
                name: 'home-users',
            },
            {
                path: 'emails',
                component: () => import('~/pages/accessemails/emails').then(m => m.default || m),
                name: 'home-emails',
            },
            {
                path: 'cargo',
                component: () => import('~/pages/cargo/cargo').then(m => m.default || m),
                name: 'home-cargo',
            },
            {
                path: 'faxes',
                component: () => import('~/pages/faxes').then(m => m.default || m),
                name: 'home-faxes',
            },
            {
                path: 'fax-counted',
                component: () => import('~/pages/Faxes/FaxCounted').then(m => m.default || m),
                name: 'home-faxes-counted',
            },
            {
                path: 'fax-info',
                component: () => import('~/pages/Faxes/FaxesInfo').then(m => m.default || m),
                name: 'home-faxes-info',
            },
            {
                path: 'sending',
                component: () => import('~/pages/Sending').then(m => m.default || m),
                name: 'home-sending',
            },
            {
                path: 'prices',
                component: () => import('~/pages/Prices').then(m => m.default || m),
                name: 'home-prices',
            },
            {
                path: 'mailing',
                component: () => import('~/pages/Mailing').then(m => m.default || m),
                name: 'home-mailing',
            },
        ],
    },

    { path: '/login', name: 'login', component: () => import('~/pages/auth/login').then(m => m.default || m) },
    { path: '/register', name: 'register', component: () => import('~/pages/auth/register').then(m => m.default || m) },

];

export function createRouter () {
    return new Router({
        routes,
        scrollBehavior,
        mode: 'history',
    });
}
