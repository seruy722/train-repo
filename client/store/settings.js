// state
export const state = () => ({
    defaultFoto: 'nofoto.jpg',
    imageUrl: '/storage/images/',
    logoImg: 'logo.png',
    adminRole: 'admin',
    allClientsObj: {
        id: 0,
        name: 'Все',
    },
    selectDatesItems: [
        {
            id: 1,
            title: 'Все даты',
        },
        {
            id: 2,
            title: 'Сегодня',
        },
        {
            id: 3,
            title: 'Текущий месяц',
        },
        {
            id: 4,
            title: 'Текущий год',
        },
        {
            id: 5,
            title: 'Текущая неделя',
        },
        {
            id: 6,
            title: 'Выбрать период',
        },
        {
            id: 7,
            title: 'Выбрать день',
        },
        {
            id: 8,
            title: 'Выбрать год',
        },
        {
            id: 9,
            title: 'Выбрать месяц',
        },
        {
            id: 10,
            title: 'Выбрать неделю',
        },
    ],
    transportItems: [
        {
            id: 0,
            title: 'Машина',
        }, {
            id: 1,
            title: 'Авиа',
        },
    ],
    commonItems: [
        {
            id: 0,
            title: 'НЕТ',
        }, {
            id: 1,
            title: 'ДА',
        },
    ],
    pageSettings: {
        title: 'Главная',
        icon: 'home',
    },
    menuMain: [
        { icon: 'home', text: 'Главная', path: '/', role: 'admin, user, moder' },
        { icon: 'people_outline', text: 'Пользователи', path: '/users', role: 'admin' },
        {
            icon: 'business',
            text: 'Карго Долги',
            model: false,
            children: [
                { icon: 'local_shipping', text: 'Карго', path: '/cargo', role: 'admin' },
                { icon: 'domain', text: 'Факсы', path: '/faxes', role: 'admin, moder' },
                { icon: 'attach_money', text: 'Цены', path: '/prices', role: 'admin, moder' },
                { icon: 'code', text: 'Свободные кода', path: '/codes', role: 'admin, moder' },
                { icon: 'toc', text: 'База клиентов', path: '/baza', role: 'admin, moder' },
            ],
        },
        // {
        //     icon: 'people',
        //     text: 'Чорный список',
        //     model: false,
        //     children: [
        //         { icon: 'home', text: 'Чорный список', path: '/black', role: 'admin' },
        //         { icon: 'list', text: 'Логи', path: '/logs', role: 'admin' },
        //     ],
        // },
        { icon: 'email', text: 'Email доступы', path: '/emails', role: 'admin' },
        { icon: 'send', text: 'Рассылка', path: '/mailing', role: 'admin' },
        { icon: 'how_to_reg', path: '/profile', text: 'Профиль', role: 'admin, user, moder' },
        { icon: 'exit_to_app', text: 'Выход', path: '/logout', role: 'admin, moder' },
    ],
    sex: [
        {
            id: 0,
            title: 'Женский',
        }, {
            id: 1,
            title: 'Мужской',
        },
    ],
});

// getters
export const getters = {
    defaultFoto: state => `${state.imageUrl}${state.defaultFoto}`,
    imageUrl: state => state.imageUrl,
    logoUrl: state => `${state.imageUrl}${state.logoImg}`,
    adminRole: state => state.adminRole,
    allClientsObj: state => state.allClientsObj,
    selectDatesItems: state => state.selectDatesItems,
    transportItems: state => state.transportItems,
    commonItems: state => state.commonItems,
    pageSettings: state => state.pageSettings,
    menuMain: state => state.menuMain,
    sex: state => state.sex,
};

export const mutations = {
    SET_PAGE_SETTINGS (state, settings) {
        state.pageSettings = settings;
    },
};

export const actions = {
    setPageSettings ({ commit }, settings) {
        commit('SET_PAGE_SETTINGS', settings);
    },
};
