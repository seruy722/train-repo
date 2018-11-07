// state
export const state = () => ({
    profileNav: [
        {path: "/", title: "Главная", icon: 'home', role: 'admin, untried, moder'},
        {path: "/profile", title: "Профиль", icon: 'how_to_reg', role: 'admin, untried, moder'},
        {path: "/logs", title: "Логи", icon: 'list', role: 'admin, moder'},
        {path: "/logout", title: "Выход", icon: 'exit_to_app', role: 'admin, untried, moder'},
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
