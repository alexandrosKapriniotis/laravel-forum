let user = window.App.user;

module.exports = {
    owns(model){
        return model['user_id'] === user.id;
    },
    isAdmin(){
        return ['Alexander Kapriniotis','JaneDoe'].includes(user.name);
    }
};
