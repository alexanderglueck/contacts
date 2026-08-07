import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

// Vite inlines VITE_* at BUILD time, and the production image builds assets with
// no .env present, so this key is always empty in an image-built bundle. Pusher
// throws on an empty key -- and because this runs before Vue mounts, that threw
// away the entire page (white screen, "You must pass your app key when you
// instantiate Pusher" in the console) rather than just disabling realtime.
//
// Broadcasting is optional here: nothing in the frontend subscribes to a channel
// yet. So only wire Echo up when a key was actually compiled in.
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
    });
}
