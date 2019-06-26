import axios from 'axios';

process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';

export default ({ app, store, redirect }) => {
    axios.defaults.baseURL = process.env.apiUrl;

    if (process.server) {
        return;
    }

    // Request interceptor
    axios.interceptors.request.use((request) => {
        request.baseURL = process.env.apiUrl;

        const token = store.getters['auth/token'];

        if (token) {
            request.headers.common['Authorization'] = `Bearer ${token}`;
        }

        const locale = store.getters['lang/locale'];
        if (locale) {
            request.headers.common['Accept-Language'] = locale;
        }

        return request;
    });

    // Response interceptor
    axios.interceptors.response.use(response => response, (error) => {
        const { status } = error.response || {};
        console.log('response_status', status);

        if (status >= 500) {
            this.$snotify.error('Произошла ошибка при запросе', {
                timeout: 3000,
                showProgressBar: true,
                closeOnClick: true,
                pauseOnHover: true,
            });
        }

        if (status === 401 && store.getters['auth/check']) {
            this.$snotify.warning('Произошла ошибка при запросе', {
                timeout: 3000,
                showProgressBar: true,
                closeOnClick: true,
                pauseOnHover: true,
            });

            store.commit('auth/LOGOUT');

            redirect({ name: 'login' });
        }

        return Promise.reject(error);
    });
};
