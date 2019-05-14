<template>
    <div
        data-vue-component-name="DestroyFaxEntry"
        class="destroy_fax_entry_component"
    >

        <v-tooltip top>

            <template v-slot:activator="{ on }">
                <v-icon
                    dark
                    color="red"
                    v-on="on"
                    @click.stop="$emit(eventName)"
                >
                    delete
                </v-icon>
            </template>

            <span>Удалить</span>
        </v-tooltip>
    </div>
</template>

<script>
    export default {
        name: 'DestroyFaxEntry',
        props: {
            eventName: {
                type: String,
                default: '',
            },
        },
    };
</script>

<style>
    .destroy_fax_entry_component{
        margin-left: 5px;
    }
</style>
