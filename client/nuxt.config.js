require('dotenv').config();

const polyfills = [
    'Promise',
    'Object.assign',
    'Object.values',
    'Array.prototype.find',
    'Array.prototype.findIndex',
    'Array.prototype.includes',
    'String.prototype.includes',
    'String.prototype.startsWith',
    'String.prototype.endsWith'
];

module.exports = {
    mode: 'spa',

    srcDir: __dirname,

    env: {
        apiUrl: process.env.APP_URL || 'http://api.laravel-nuxt.test',
        appName: process.env.APP_NAME || 'Cargo007',
        appLocale: process.env.APP_LOCALE || 'en',
        githubAuth: !!process.env.GITHUB_CLIENT_ID,
    },

    head: {
        title: process.env.APP_NAME,
        titleTemplate: '%s - ' + process.env.APP_NAME,
        meta: [
            {charset: 'utf-8'},
            {name: 'viewport', content: 'width=device-width, initial-scale=1'},
            {hid: 'description', name: 'description', content: 'Nuxt.js project'}
        ],
        link: [
            {rel: 'icon', type: 'image/x-icon', href: '/favicon.ico'},
            {
                rel: 'stylesheet',
                type: 'text/css',
                href: 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons'
            },
        ],
        script: [
            {src: `https://cdn.polyfill.io/v2/polyfill.min.js?features=${polyfills.join(',')}`},
            {src: 'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js'},
        ]
    },

    loading: {color: '#007bff'},

    router: {
        middleware: ['locale', 'check-auth'],
    },

    css: [
        {src: '~assets/sass/app.scss', lang: 'scss'},
        { src: '~/assets/css/main.css', lang: 'css' },
        { src: '~/assets/css/app.styl', lang: 'styl' }
    ],

    plugins: [
        // '~components/global',
        '~plugins/i18n',
        '~plugins/vform',
        '~plugins/axios',
        '~plugins/fontawesome',
        '~plugins/nuxt-client-init',
        '~plugins/vuetify',
        {src: '~plugins/bootstrap', ssr: false},
        '~/plugins/vue-snotify',
    ],

    modules: [
        '@nuxtjs/router',
        '~/modules/spa',
    ],

    build: {
        extractCSS: true,
    }
};
