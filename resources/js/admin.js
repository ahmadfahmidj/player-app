import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: false,
    enabledTransports: ['ws'],
    disableStats: true,
});

// Listen for broadcast events to update admin UI
document.addEventListener('DOMContentLoaded', () => {
    window.Echo.channel('tv-broadcast')
        .listen('VideoPlayed', () => updateStatusUI(true))
        .listen('VideoPaused', () => updateStatusUI(false))
        .listen('VideoChanged', (e) => {
            updateStatusUI(true);
        })
        .listen('LoopModeChanged', (e) => {
            const el = document.getElementById('current-loop');
            if (el) { el.textContent = e.loop_mode; }
            const sel = document.getElementById('loop-select');
            if (sel) { sel.value = e.loop_mode; }
        })
        .listen('RunningTextUpdated', () => {})
        .listen('LogoUpdated', () => {});
});

function updateStatusUI(isPlaying) {
    const indicator = document.getElementById('status-indicator');
    const text = document.getElementById('status-text');
    if (indicator) {
        indicator.className = 'w-3 h-3 rounded-full ' + (isPlaying ? 'bg-green-500' : 'bg-gray-400');
    }
    if (text) {
        text.textContent = isPlaying ? 'Playing' : 'Paused';
    }
}
