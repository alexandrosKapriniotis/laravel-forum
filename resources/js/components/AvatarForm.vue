<template>
    <div>
        <div class="level">
            <img :src="avatar" width="50" height="50" alt="profile image" class="mr-1" />

            <h1 v-text="user.name"></h1>
        </div>

        <form v-if="canUpdate" method="POST" enctype="multipart/form-data">
            <image-upload name="avatar" @loaded="onLoad"></image-upload>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </div>
</template>

<script>
import ImageUpload from './ImageUpload.vue';
export default {
    name: "AvatarForm",
    props: ['user'],
    components: { ImageUpload },
    data() {
      return {
          avatar: this.user.avatar_path
      }
    },
    computed: {
        canUpdate() {
            return this.authorize(user => user.id === this.user.id)
        }
    },
    methods: {
        onLoad(avatar){
           this.avatar = avatar.src;

           this.persist(avatar.file);
        },
        persist(avatar){
            let data = new FormData();

            data.append('avatar',avatar);

            axios.post(`/api/users/${this.user.name}/avatar`,data)
                .then(() => flash('Avatar uploaded!'));
        }
    }
}
</script>

<style scoped>

</style>
