import { computed, ref } from 'vue';

/**
 * Tracks how many dimming overlays (SlideOver) are currently open.
 *
 * A SlideOver covers the page with a translucent backdrop, so the flash banner
 * that AppLayout renders in <main> ends up behind it: visible, but greyed out,
 * and nowhere near the control the user just used. While an overlay is open the
 * layout hides its banner and the overlay renders one inside its own panel, so
 * the message shows up once, where the action happened.
 */
const openCount = ref(0);

export const overlayOpen = computed(() => openCount.value > 0);

export function registerOverlay() {
    openCount.value += 1;
}

export function unregisterOverlay() {
    openCount.value = Math.max(0, openCount.value - 1);
}
