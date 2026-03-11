import 'video.js/dist/video-js.css';
import videojs from 'video.js';
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

let player = null;
let currentVideoId = null;
let loopMode = 'none';
let videoList = [];
let channelSlug = window.PLAYER_INITIAL ? window.PLAYER_INITIAL.channelSlug : 'main';

async function initPlayer() {
    // Fetch state from server
    const stateRes = await fetch(`/api/player/state?channel=${channelSlug}`);
    const state = await stateRes.json();

    loopMode = state.loop_mode || 'none';
    currentVideoId = state.current_video_id;

    // Init video.js
    player = videojs('my-video', {
        controls: false,
        autoplay: false,
        preload: 'auto',
        fluid: false,
        fill: true,
        responsive: false,
    });

    // Update ticker & logo from state
    if (state.running_text) {
        document.getElementById('ticker-text').textContent = state.running_text;
    }
    if (state.logo_url) {
        const logo = document.getElementById('logo');
        logo.src = state.logo_url;
        logo.style.display = '';
    }

    if (state.overlay) {
        updateOverlay(state.overlay);
    }
    
    if (state.video_url) {
        player.src({ type: 'video/mp4', src: state.video_url });
        player.one('loadedmetadata', () => {
            player.currentTime(state.current_position || 0);
            if (state.is_playing) {
                attemptPlay(player);
            }
        });
    }

    // Handle video end
    player.on('ended', () => handleVideoEnded());

    // Subscribe to broadcast channel
    window.Echo.channel(`tv-broadcast.${channelSlug}`)
        .listen('VideoPlayed', (e) => {
            const drift = (Date.now() / 1000) - e.timestamp;
            player.currentTime(e.position + drift);
            attemptPlay(player);
        })
        .listen('VideoPaused', (e) => {
            player.currentTime(e.position);
            player.pause();
        })
        .listen('VideoSeeked', (e) => {
            const drift = (Date.now() / 1000) - e.timestamp;
            player.currentTime(e.position + drift);
        })
        .listen('VideoChanged', (e) => {
            currentVideoId = e.video_id;
            loopMode = e.loop_mode;
            // Fetch the video URL from state
            fetch('/api/player/state').then(r => r.json()).then(s => {
                if (s.video_url) {
                    player.src({ type: 'video/mp4', src: s.video_url });
                    player.one('loadedmetadata', () => {
                        player.currentTime(0);
                        attemptPlay(player);
                    });
                }
            });
        })
        .listen('LoopModeChanged', (e) => {
            loopMode = e.loop_mode;
        })
        .listen('RunningTextUpdated', (e) => {
            document.getElementById('ticker-text').textContent = e.text;
        })
        .listen('LogoUpdated', (e) => {
            const logo = document.getElementById('logo');
            logo.src = e.logo_url;
            logo.style.display = '';
        })
        .listen('EventOverlayUpdated', (e) => {
            updateOverlay(e.overlayData);
        });
}

function updateOverlay(data) {
    const overlay = document.getElementById('event-overlay');
    if (!overlay) return;

    if (data.show) {
        overlay.style.display = 'block';
        document.getElementById('overlay-location').textContent = data.location;
        document.getElementById('overlay-subtitle').textContent = data.subtitle;
        document.getElementById('overlay-title').textContent = data.title;
        document.getElementById('overlay-time').textContent = data.time;
        document.getElementById('overlay-organizer').textContent = data.organizer;
    } else {
        overlay.style.display = 'none';
    }
}


function attemptPlay(playerInstance) {
    const promise = playerInstance.play();
    if (promise !== undefined) {
        promise.catch(() => {
            playerInstance.muted(true);
            playerInstance.play();
        });
    }
}

function handleVideoEnded() {
    fetch(`/api/player/video-ended?channel=${channelSlug}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
        .then(r => r.json())
        .then(data => {
            if (data.action === 'repeat') {
                player.currentTime(0);
                attemptPlay(player);
            }
            // 'next' is handled via the VideoChanged broadcast event
            // 'stop' requires no client action — video stays ended
        })
        .catch(() => {
            // Fallback: if server is unreachable, handle single loop client-side
            if (loopMode === 'single') {
                player.currentTime(0);
                attemptPlay(player);
            }
        });
}

function updateTickerBadgeClock() {
    const clock = document.getElementById('ticker-clock');
    const badge = document.getElementById('ticker-badge');
    const target = clock || badge;
    if (!target) return;

    const now = new Date();
    const formatted = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }) + ' \u00A0|\u00A0 ' + now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });

    target.textContent = formatted;
}

document.addEventListener('DOMContentLoaded', () => {
    initPlayer();
    updateTickerBadgeClock();
    setInterval(updateTickerBadgeClock, 1000);
});
