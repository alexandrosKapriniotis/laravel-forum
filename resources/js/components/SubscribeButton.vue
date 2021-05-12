<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
export default {
name: "SubscribeButton",
    props: ['active'],
    data() {
        return {
            isActive: this.active
        }
    },
    computed: {
        classes() {
            return ['btn',this.isActive ? 'btn-primary' : 'btn-outline-dark'];
        }
    },
    methods: {
        subscribe() {
            axios[
                (this.isActive ? 'delete' : 'post')
                ](location.pathname + '/subscriptions');

            this.isActive = ! this.isActive;
        }
    }
}
</script>

<style scoped>

</style>
