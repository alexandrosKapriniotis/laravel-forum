<template>

</template>

<script>
import Replies from '../components/Replies.vue';
import SubscribeButton from "../components/SubscribeButton";

export default {
    name: "Thread",
    components: { Replies,SubscribeButton },
    props: ['thread'],
    data(){
        return {
            repliesCount: this.thread.replies_count,
            locked: this.thread.locked
        }
    },
    methods: {
        toggleLock() {
            axios[this.locked ? 'delete' : 'post']('/locked-threads/'+this.thread.slug);

            this.locked = ! this.locked;
        }
    }
}
</script>

<style scoped>

</style>
