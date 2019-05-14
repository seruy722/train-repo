<template>
    <div
        data-vue-component-name="DefaultButton"
        class="default_button"
    >
        <v-btn
            :disabled="disabledButton"
            :loading="disabledButton"
            :color="defaultButtonSettings.color"
            :flat="defaultButtonSettings.flat"
            :outline="defaultButtonSettings.outline"
            :dark="defaultButtonSettings.dark"
            :fab="defaultButtonSettings.fab"
            :small="defaultButtonSettings.small"

            @click="$emit('clickButton')"
        >
            {{ defaultButtonSettings.title }}
            <v-icon dark>{{ defaultButtonSettings.icon }}</v-icon>

        </v-btn>
    </div>

</template>

<script>
    export default {
        name: 'DefaultButton',
        props: {
            defaultButtonSettings: {
                type: Object,
                default: () => null,
            },
            disabledButton: {
                type: Boolean,
                default: false,
            },
        },
    };
</script>

<style lang="scss" scoped>
    .default_button {
        margin: 0 5px;
    }
</style>
