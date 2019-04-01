<template>

    <div
        data-vue-component-name="error"
    >
        <template>
            <div class="container">
                <h1 v-if="error.statusCode === 404">Page not found</h1>
                <h1 v-else>An error occurred</h1>
                <nuxt-link to="/">Home page</nuxt-link>
            </div>
        </template>
    </div>

</template>

<script>
    export default {
        name: 'error',
        props: ['error'],
    };
</script>
