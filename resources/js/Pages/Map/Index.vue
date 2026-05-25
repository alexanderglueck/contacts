<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const mapContainer = ref(null);
let map = null;
let markersLayer = null;
let oms = null;
const popup = L.popup();
let fetchToken = 0;

const defaultIcon = L.icon({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
}[c]));

const renderPopup = (m) => {
    const lines = [
        m.street,
        [m.zip, m.city].filter(Boolean).join(' '),
        m.state,
        m.country,
    ].filter(Boolean);

    return `
        <div class="space-y-1 text-sm">
            <p class="font-medium text-gray-900">${escapeHtml(m.title)}</p>
            <p class="text-xs text-gray-500">${escapeHtml(m.name)}</p>
            ${lines.map((l) => `<p class="text-xs text-gray-600">${escapeHtml(l)}</p>`).join('')}
            <a href="${route('contacts.show', { contact: m.contact_ulid })}" class="block pt-1 text-xs text-indigo-600 hover:text-indigo-500 underline">
                ${escapeHtml(t('map.open_contact'))}
            </a>
        </div>
    `;
};

const fetchMarkers = async () => {
    if (! map) return;

    const bounds = map.getBounds();
    const boundsParam = [
        bounds.getSouth(),
        bounds.getWest(),
        bounds.getNorth(),
        bounds.getEast(),
    ].join(',');

    const requestToken = ++fetchToken;

    try {
        const { data } = await axios.post(route('map.contacts'), { bounds: boundsParam });

        if (requestToken !== fetchToken) return;

        oms.unspiderfy();
        oms.clearMarkers();
        markersLayer.clearLayers();

        data.forEach((m) => {
            const marker = L.marker([m.latitude, m.longitude], { icon: defaultIcon, title: m.title });
            // Stash the popup HTML on the marker; OMS's click listener reads it
            // and feeds the shared popup, so co-located markers still get
            // individual popups after they spider out.
            marker._popupHtml = renderPopup(m);
            markersLayer.addLayer(marker);
            oms.addMarker(marker);
        });
    } catch (e) {
        if (requestToken === fetchToken) {
            console.error('Failed to load contacts on map', e);
        }
    }
};

onMounted(async () => {
    map = L.map(mapContainer.value, {
        center: [47.0707, 15.4395], // Graz, AT — sensible default; first fetch will adjust
        zoom: 5,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors',
    }).addTo(map);

    markersLayer = L.layerGroup().addTo(map);

    // OMS handles the "two pins on the same house" case: clicking such a stack
    // fans the markers out so each is individually clickable. The package is a
    // legacy UMD that attaches itself to window and reads window.L, so we have
    // to bridge L onto the window before its IIFE evaluates — dynamic-importing
    // inside onMounted guarantees that ordering.
    window.L = window.L || L;
    await import('overlapping-marker-spiderfier-leaflet');
    oms = new window.OverlappingMarkerSpiderfier(map, {
        keepSpiderfied: true,
        nearbyDistance: 20,
    });

    oms.addListener('click', (marker) => {
        popup
            .setLatLng(marker.getLatLng())
            .setContent(marker._popupHtml)
            .openOn(map);
    });

    map.on('popupopen', (event) => {
        const link = event.popup.getElement()?.querySelector('a[href]');
        if (! link) return;
        link.addEventListener('click', (e) => {
            e.preventDefault();
            router.visit(link.href);
        });
    });

    map.on('moveend', fetchMarkers);
    fetchMarkers();
});

onBeforeUnmount(() => {
    if (oms) {
        oms.clearListeners('click');
        oms.clearMarkers();
        oms = null;
    }
    if (map) {
        map.off();
        map.remove();
        map = null;
    }
});
</script>

<template>
    <AppLayout :title="t('map.title')">
        <Head :title="t('map.title')" />

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">{{ t('map.heading') }}</h2>
            </div>
            <div ref="mapContainer" class="h-[70vh] w-full"></div>
        </div>
    </AppLayout>
</template>
