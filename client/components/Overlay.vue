<template>
    <div
        data-vue-component-name="Overlay"
    >
        <template>
            <div class="text-xs-center">
                <!--<v-btn-->
                    <!--color="deep-purple accent-4"-->
                    <!--class="white&#45;&#45;text"-->
                    <!--@click="overlay = !overlay"-->
                <!--&gt;-->
                    <!--Launch Application-->
                    <!--<v-icon right>mdi-open-in-new</v-icon>-->
                <!--</v-btn>-->

                <v-overlay :value="overlay">
                    <v-progress-circular indeterminate size="64"></v-progress-circular>
                </v-overlay>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: 'Overlay',
        props: {
            overlay: {
                type: Boolean,
                default: false,
            },
        },
    };
</script>
