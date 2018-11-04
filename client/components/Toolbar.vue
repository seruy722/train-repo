<template>
    <v-card
        color="grey lighten-4"
        flat
        tile
    >
        <v-toolbar dense color="primary">
            <v-toolbar-side-icon></v-toolbar-side-icon>

            <v-toolbar-title>Title</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn icon>
                <v-icon>search</v-icon>
            </v-btn>

            <v-btn icon>
                <v-icon>favorite</v-icon>
            </v-btn>

            <v-btn icon>
                <v-icon>more_vert</v-icon>
            </v-btn>
            <nuxt-link to="/login">Вход</nuxt-link>
            <v-spacer></v-spacer>
            <nuxt-link to="/register">Регистрация</nuxt-link>
        </v-toolbar>
    </v-card>
</template>
