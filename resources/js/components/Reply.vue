<template>
    <div :id="'reply-'+this.id" class="card mt-2 mb-2" >
        <div class="card-header" :class="isBest ? 'bg-success' : ''">
            <div class="level">

                <a :href="'profiles/'+ reply.owner.name" class="card-title flex" v-text="reply.owner.name"></a>

                <span style="padding: 0 5px;">
                    said <span v-text="ago"></span>
                </span>

                <div v-if="signedIn">
                    <favorite :reply="reply" v-cloak></favorite>
                </div>
            </div>

        </div>

        <div class="card-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body" ></textarea>
                </div>

                <button class="btn btn-sm btn-primary" @click="update">Update</button>
                <button class="btn btn-sm btn-link" @click="editing = false">Cancel</button>
            </div>

            <div v-else v-html="body"></div>
        </div>

        <div class="card-footer level" v-if="authorize('owns', reply) || authorize('owns',reply.thread)">

            <div v-if="authorize('owns',reply)">
                <button class="btn btn-sm btn-outline-primary mr-1" @click="editing=true">Edit</button>
                <button class="btn btn-sm btn-danger" @click="destroy">Delete</button>
            </div>

            <button class="btn btn-sm btn-default mr-1 ml-auto" @click="markBestReply" v-show="! isBest" v-if="authorize('owns', reply.thread)">Best Reply?</button>
        </div>
    </div>
</template>
<script>
    import Favorite from "./Favorite";
    import moment   from 'moment';
    export default {
        props: ['reply'],
        components: { Favorite },
        data(){
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.isBest
            };
        },
        computed: {
            ago(){
                return moment(this.reply.created_at).fromNow();
            }
        },
        created() {
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id)
            })
        },
        methods: {
            update(){
                axios.patch('/replies/' + this.reply.id,{
                    body: this.body
                });

                this.editing = false;

                flash('Updated');
            },
            destroy(){
                axios.delete('/replies/' + this.reply.id);

                this.$emit('deleted',this.reply.id);
            },
            markBestReply(){
                this.isBest = true;

                axios.post('/replies/'+ this.id + '/best');

                window.events.$emit('best-reply-selected',this.id);
            }
        }
    }
</script>

<style scoped>

</style>
