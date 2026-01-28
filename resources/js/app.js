import './bootstrap';
import { createApp } from 'vue';
import ContactPresenceChannel from './components/ContactPresenceChannel.vue';
import Cropper from 'cropperjs';

const app = createApp({});

app.component('contact-presence-channel', ContactPresenceChannel);

app.mount('#app');

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
