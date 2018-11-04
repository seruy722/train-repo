// state
export const state = () => ({
    profileNav: [
        {path: "/", title: "Главная", icon: 'home', role: 'admin'},
        {path: "/profile", title: "Профиль", icon: 'how_to_reg', role: 'admin, untried'},
        {path: "/logs", title: "Логи", icon: 'list', role: 'admin'},
        {path: "/logout", title: "Выход", icon: 'exit_to_app', role: 'admin, untried'},
    ],
});

// getters
export const getters = {
    profileNav: state => state.profileNav,
};

// mutations
export const mutations = {};

// actions
export const actions = {};
