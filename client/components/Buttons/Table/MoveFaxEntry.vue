<template>
    <div
        data-vue-component-name="MoveFaxEntry"
        class="move_fax_entry_component"
    >
        <v-tooltip top>

            <template v-slot:activator="{ on }">
                <v-icon
                    dark
                    color="cyan"
                    v-on="on"
                    @click.stop="$emit(eventName)"
                >
                    open_with
                </v-icon>
            </template>

            <span>Переместить</span>
        </v-tooltip>
    </div>
</template>

<script>
    export default {
        name: 'MoveFaxEntry',
        props: {
            eventName: {
                type: String,
                default: '',
            },
        },
    };
</script>

<style type="scss" scoped>
    .move_fax_entry_component {
        margin-left: 5px;
    }
</style>
