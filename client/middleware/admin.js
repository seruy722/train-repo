export default ({store, redirect}) => {
    if (store.getters['auth/role'] !== store.getters['settings/adminRole']) {
        return redirect('/');
    }
}
