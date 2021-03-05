<template>
    <ul>
        <li v-for="user in users" v-text="user.name"></li>
    </ul>
</template>
<script>
    export default {
        props: [
            'contactId',
            'tenantId'
        ],
        data() {
            return {
                users: []
            };
        },
        mounted() {
            // code to listen to a private event in a channel and presence channel events
            window.Echo.join(`p.contact.${this.tenantId}.${this.contactId}`)
                .here((users) => {
                    this.users = users;
                })
                .joining((user) => {
                    console.log(user.name + " is joining");
                    this.users.push(user);
                })
                .leaving((user) => {
                    console.log(user.name + " is leaving");
                    this.users = this.users.filter(existingUser => existingUser.id !== user.id)
                })
                .listen('WebsocketTest', (e) => {
                    console.log(e);
                });

            /*// code to listen to a private event
            window.Echo.private(`contact.${this.tenantId}.${this.contactId}`)
                .listen('WebsocketTest', (e) => {
                    console.log(e);
                });*/

            console.log('Component mounted.')
        }
    }
</script>
