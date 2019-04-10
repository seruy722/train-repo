<template>
    <div
        data-vue-component-name="RectangleBtn"
    >
        <v-btn
            :color="color"
            :flat="flat"
            @click="$emit(event)"
        >
            {{ title }}
        </v-btn>
    </div>
</template>

<script>
    export default {
        name: 'RectangleBtn',
        props: {
            title: {
                type: String,
                default: 'Сохранить',
            },
            color: {
                type: String,
                default: 'success',
            },
            flat: {
                type: Boolean,
                default: false,
            },
            event: {
                type: String,
                required: true,
            },
        },
    };
</script>
