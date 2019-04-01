<template>

    <div
        data-vue-component-name="Connectivity"
    >
        <template>
            <!-- @detected-condition fires when the connectivity status of the device changes -->
            <offline @detected-condition="handleConnectivityChange">
                <!-- Only renders when the device is online -->
                <div slot="online">
                    <p>It looks like you're online! Here's all the things you can do...</p>

                </div>
                <!-- Only renders when the device is offline -->
                <div slot="offline">
                    <p>You appear to be offline, that's okay, we can still do things...</p>

                </div>
            </offline>
        </template>
    </div>

</template>

<script>
    import offline from 'v-offline';

    export default {
        name: 'Connectivity',
        components: {
            offline,
        },

        methods: {
            handleConnectivityChange (status) {
                console.log(status);
            },
        },
    };
</script>
