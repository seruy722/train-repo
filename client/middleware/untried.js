export default ({store, redirect}) => {
    if (store.getters['auth/role']=== 'untried') {
        return redirect('/profile');
    }
}
