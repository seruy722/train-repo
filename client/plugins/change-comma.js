import Vue from 'vue';

// Регистрируем глобальную пользовательскую директиву `v-focus`
Vue.directive('change', {
    bind: function (el, binding, vnode) {
        console.log('bind');
    },
    // Когда привязанный элемент вставлен в DOM...
    inserted: function (el, binding, vnode) {
        console.log('el', el);
        console.log('binding', binding);
        console.log('vnode', vnode);
    },
    update: function (el, binding, vnode) {
        console.log('update');
    },
});
