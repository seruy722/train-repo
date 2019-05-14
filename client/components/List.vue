<template>
    <div>
        <li
            v-for="link in links"
            :key="link.bookmarked"
        >
            <slot
                name="link"
                :link="link"
                :value="value"
                :markBtn="markBtn"
            ></slot>
        </li>
    </div>

</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                default: '',
            },
        },
        data () {
            return {
                links: [
                    { href: 'http://...', title: 'First Link', bookmarked: true },
                    { href: 'http://...', title: 'Second Link', bookmarked: false },
                    // ...
                ]
            }
        },
        methods: {
            markBtn (link) {
                console.log('LINK', link);
            }
        }
    }
</script>
