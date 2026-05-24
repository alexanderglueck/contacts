<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

const props = defineProps({
    latitude: { type: [Number, String], required: true },
    longitude: { type: [Number, String], required: true },
    zoom: { type: Number, default: 15 },
});

const container = ref(null);
let map = null;
let marker = null;

const icon = L.icon({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    shadowSize: [41, 41],
});

const coords = () => [parseFloat(props.latitude), parseFloat(props.longitude)];

onMounted(() => {
    map = L.map(container.value, {
        center: coords(),
        zoom: props.zoom,
        scrollWheelZoom: false,   // avoid hijacking scroll inside slide-overs
        zoomControl: true,
        attributionControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
    }).addTo(map);

    marker = L.marker(coords(), { icon }).addTo(map);
});

watch(
    () => [props.latitude, props.longitude],
    () => {
        if (! map) return;
        map.setView(coords(), props.zoom);
        if (marker) marker.setLatLng(coords());
    },
);

onBeforeUnmount(() => {
    if (map) {
        map.off();
        map.remove();
        map = null;
    }
});
</script>

<template>
    <div ref="container" class="h-48 w-full rounded-md overflow-hidden"></div>
</template>
