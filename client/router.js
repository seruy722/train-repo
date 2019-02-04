import Vue from 'vue';
import Router from 'vue-router';
import { scrollBehavior } from '~/utils';

Vue.use(Router);
// Main page
const Home = () => import('~/pages/home').then(m => m.default || m);
// BlackList users
const BlackList = () => import('~/pages/blacklist/blacklist').then(m => m.default || m);
// Logs for blacklist users
const Logs = () => import('~/pages/blacklist/logs').then(m => m.default || m);
// Profile users
const Profile = () => import('~/pages/users/profile').then(m => m.default || m);
// Auth
const Login = () => import('~/pages/auth/login').then(m => m.default || m);
const Register = () => import('~/pages/auth/register').then(m => m.default || m);
// Edit users data
const Users = () => import('~/pages/users/users').then(m => m.default || m);
// Emails for access to site
const Emails = () => import('~/pages/accessemails/emails').then(m => m.default || m);

// Cargo && Debts
const Cargo = () => import('~/pages/cargo/cargo').then(m => m.default || m);

// Faxes
const Faxes = () => import('~/pages/faxes').then(m => m.default || m);

const routes = [
    {
        path: '/',
        component: Home,
        children: [
            {
                path: '/',
                component: BlackList,
                name: 'home-blacklist',
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
            {
                path: 'users',
                component: Users,
                name: 'home-users',
            },
            {
                path: 'emails',
                component: Emails,
                name: 'home-emails',
            },
            {
                path: 'cargo',
                component: Cargo,
                name: 'home-cargo',
            },
            {
                path: 'faxes',
                component: Faxes,
                name: 'home-faxes',
            },
        ],
    },

    { path: '/login', name: 'login', component: Login },
    { path: '/register', name: 'register', component: Register },

];

export function createRouter () {
    return new Router({
        routes,
        scrollBehavior,
        mode: 'history',
    });
}
