<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
            <textarea
                name="body"
                id="body"
                class="form-control"
                placeholder="Have something to say?"
                rows="5"
                required
                v-model="body">

            </textarea>
            </div>

            <button
                type="submit"
                class="btn btn-primary"
                @click="addReply">Post</button>
        </div>


        <p class="text-center" v-else>Please <a href="">Sign in</a> to participate in this discussion</p>
    </div>
</template>

<script>
export default {
    name: "NewReply",
    data(){
        return {
            body: ''
        };
    },
    computed: {
        signedIn(){
            return window.App.signedIn
        }
    },
    methods: {
        addReply(){
            axios.post(location.pathname + '/replies', { body: this.body })
                .catch(error => {
                    flash(error.response.data,'danger')
                })
                .then(({data}) => {
               this.body = '';

               flash('Your reply has been posted');

               this.$emit('created', data);
            });
        }
    }
}
</script>

<style scoped>

</style>
