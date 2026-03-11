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
    const channelSlugMeta = document.querySelector('meta[name="channel-slug"]');
    const channelSlug = channelSlugMeta ? channelSlugMeta.content : 'default';
    
    window.Echo.channel('tv-broadcast.' + channelSlug)
        .listen('VideoPlayed', (e) => {
            updateStatusUI(true);
            if (e.video_id) updatePlaylistUI(e.video_id);
        })
        .listen('VideoPaused', () => updateStatusUI(false))
        .listen('VideoChanged', (e) => {
            updateStatusUI(true);
            if (e.video_id) updatePlaylistUI(e.video_id);
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
    const ping = document.getElementById('status-ping');
    const text = document.getElementById('status-text');
    
    if (indicator) {
        indicator.className = 'relative inline-flex rounded-full h-5 w-5 ' + 
            (isPlaying ? 'bg-green-500 border border-green-700 shadow-sm' : 'bg-gray-400 border border-gray-500 inset-shadow-sm');
    }
    if (ping) {
        ping.className = 'absolute inline-flex h-full w-full rounded-full opacity-75 ' + 
            (isPlaying ? 'animate-ping bg-green-400' : 'hidden');
    }
    if (text) {
        text.className = 'text-sm font-bold uppercase tracking-wider ' + 
            (isPlaying ? 'text-green-600' : 'text-gray-500');
        text.textContent = isPlaying ? '▶ Playing' : '■ Paused';
    }
}

function updatePlaylistUI(videoId) {
    const items = document.querySelectorAll('[data-playlist-item]');
    let currentTitle = 'No video selected';

    items.forEach(item => {
        const id = parseInt(item.getAttribute('data-playlist-item'));
        const indicator = item.querySelector('[data-playlist-indicator]');
        const titleEl = item.querySelector('[data-playlist-title]');
        const isCurrent = (id === videoId);

        if (isCurrent) {
            item.className = 'group flex items-center gap-3 px-3 py-2 cursor-default bg-orange-50 border-l-4 border-orange-500';
            if (indicator) {
                indicator.innerHTML = '<span class="text-orange-500 animate-pulse">▶</span>';
            }
            if (titleEl) {
                titleEl.className = 'text-xs font-bold truncate text-gray-900';
                currentTitle = titleEl.textContent.trim();
            }
        } else {
            item.className = 'group flex items-center gap-3 px-3 py-2 cursor-default hover:bg-blue-50 border-l-4 border-transparent';
            if (indicator) {
                const idx = indicator.getAttribute('data-playlist-index') || '';
                indicator.innerHTML = idx;
            }
            if (titleEl) {
                titleEl.className = 'text-xs font-bold truncate text-gray-700';
            }
        }
    });

    const currentTitleEl = document.getElementById('current-video-title');
    if (currentTitleEl && currentTitle !== 'No video selected') {
        currentTitleEl.textContent = currentTitle;
    }
}
