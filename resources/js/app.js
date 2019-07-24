/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('contact-presence-channel', require('./components/ContactPresenceChannel.vue').default);

const app = new Vue({
    el: '#app'
});


import Cropper from 'cropperjs';

const image = document.getElementById('cropper-image');
if (image) {
    const cropper = new Cropper(image, {
        aspectRatio: 4 / 4,
        preview: ".img-preview",
        crop(event) {
            $("#image-x").val(event.detail.x);
            $("#image-y").val(event.detail.y);
            $("#image-width").val(event.detail.width);
            $("#image-height").val(event.detail.height);
        },
    });

    const $inputImage = $("#inputImage");
    if (window.FileReader) {
        $inputImage.change(function () {
            var fileReader = new FileReader(),
                files = this.files,
                file;

            if (!files.length) {
                return;
            }

            file = files[0];

            if (/^image\/\w+$/.test(file.type)) {
                fileReader.readAsDataURL(file);
                fileReader.onload = function () {
                    cropper.reset().replace(this.result);
                };
            } else {
                alert("Please choose an image file.");
            }
        });
    } else {
        $inputImage.addClass("hide");
    }
}
