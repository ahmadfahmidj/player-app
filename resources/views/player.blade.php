<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimum-scale=1.0">
    <title>Hospital TV</title>
    @vite(['resources/css/app.css', 'resources/js/player.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #050505;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        #video-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        #logo {
            position: absolute;
            top: 2rem;
            left: 2rem;
            max-height: 80px;
            max-width: 220px;
            object-fit: contain;
            z-index: 40;
            transition: opacity 0.5s ease-in-out;
        }

        #my-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .video-js {
            width: 100% !important;
            height: 100% !important;
            background-color: transparent !important;
        }

        .vjs-tech {
            object-fit: fill !important;
            width: 100% !important;
            height: 100% !important;
        }

        /* Ticker - TV-optimized (no backdrop-filter) */
        #ticker-glass {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1.25rem 0;
            z-index: 30;
            background: rgba(2, 6, 23, 0.85);
            border-top: 2px solid rgba(56, 189, 248, 0.6);
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        #ticker-badge {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 2.5rem 0 2rem;
            background: linear-gradient(90deg, #0f172a, #1e3a8a);
            color: #f8fafc;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            z-index: 35;
            min-width: 180px;
            border-right: 4px solid #38bdf8;
            border-top-right-radius: 1.5rem;
            border-bottom-right-radius: 1.5rem;
            box-shadow: 6px 0 20px rgba(0, 0, 0, 0.5);
        }

        #ticker-badge svg {
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 0.75rem;
            color: #38bdf8;
            flex-shrink: 0;
        }

        #ticker-bar {
            flex-grow: 1;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        #ticker-text {
            display: inline-block;
            padding-left: 100vw;
            animation: marquee 25s linear infinite;
            font-size: 1.5rem;
            font-weight: 600;
            color: #f8fafc;
            letter-spacing: 0.05em;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Portrait Adjustments for Ticker */
        body.is-portrait #ticker-glass {
            padding: 0.65rem 0;
            border-top-width: 1px;
        }

        body.is-portrait #ticker-badge {
            padding: 0 1.25rem 0 1rem;
            font-size: 0.85rem;
            min-width: 120px;
            border-right-width: 2px;
            border-top-right-radius: 1rem;
            border-bottom-right-radius: 1rem;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.4);
        }

        body.is-portrait #ticker-badge svg {
            width: 1.1rem;
            height: 1.1rem;
            margin-right: 0.4rem;
        }

        body.is-portrait #ticker-text {
            font-size: 1rem;
            letter-spacing: 0.03em;
        }

        /* Event Overlay - TV-optimized (no backdrop-filter) */
        #event-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            z-index: 25;
            background: rgba(2, 6, 23, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 2rem;
            padding: 3rem 4rem;
            width: 80%;
            max-width: 1200px;
            color: white;
            text-align: center;
            display: none;
            box-sizing: border-box;
        }

        .overlay-location {
            font-size: 3.5rem;
            font-weight: 900;
            color: #fce7f3;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        .overlay-main-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1.5rem;
            padding: 3rem 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .overlay-subtitle {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fbbf24;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .overlay-title {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            text-transform: uppercase;
            margin-bottom: 3rem;
        }

        .overlay-details-grid {
            display: flex;
            justify-content: space-between;
        }

        .overlay-detail-card {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 48%;
            /* Compat sizing */
        }

        .overlay-icon {
            color: #fbbf24;
            width: 3rem;
            height: 3rem;
            flex-shrink: 0;
            margin-right: 1.5rem;
        }

        .overlay-detail-content {
            flex-grow: 1;
        }

        .overlay-detail-label {
            font-size: 0.9rem;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .overlay-detail-value {
            font-size: 1.5rem;
            font-weight: 700;
        }
    </style>
</head>

<body class="{{ $screenOrientation == 'portrait' ? 'is-portrait' : '' }}">
    <x-instruckt-toolbar />
    @php
        $rotStyle = '';
        if ($channel->orientation == 90) {
            $rotStyle =
                '-webkit-transform: rotate(90deg); transform: rotate(90deg); -webkit-transform-origin: center; transform-origin: center;';
        } elseif ($channel->orientation == 180) {
            $rotStyle =
                '-webkit-transform: rotate(180deg); transform: rotate(180deg); -webkit-transform-origin: center; transform-origin: center;';
        } elseif ($channel->orientation == 270) {
            $rotStyle =
                '-webkit-transform: rotate(-90deg); transform: rotate(-90deg); -webkit-transform-origin: center; transform-origin: center;';
        }

        $screenStyle = '';
        if ($screenOrientation == 'portrait') {
            // Hardcode purely in style for legacy TVs with CSS variables fallback
            $screenStyle =
                'position: absolute; top: 0; left: 100%; width: 100vh; height: 100vw; -webkit-transform-origin: top left; transform-origin: top left; -webkit-transform: rotate(90deg); transform: rotate(90deg); overflow: hidden;';
        } else {
            // Full landscape explicitly natively using percentages to avoid vw/vh bugs on ancient WebViews
            $screenStyle =
                'position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; overflow: hidden;';
        }

        // Combine transforms safely if both exist
        if ($screenOrientation == 'portrait' && $rotStyle != '') {
            $screenStyle =
                'position: absolute; top: 0; left: 100%; width: 100vh; height: 100vw; -webkit-transform-origin: top left; transform-origin: top left; -webkit-transform: rotate(90deg) rotate(' .
                $channel->orientation .
                'deg); transform: rotate(90deg) rotate(' .
                $channel->orientation .
                'deg); overflow: hidden;';
        } elseif ($screenOrientation != 'portrait' && $rotStyle != '') {
            $screenStyle .= ' ' . $rotStyle;
        }
    @endphp

    <!-- Un-rotated Video Container (Hardware Native) -->
    <div id="video-bg" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; z-index: 5; overflow: hidden; background: #000;">
        <video id="my-video" class="video-js vjs-big-play-centered" playsinline>
            <p class="vjs-no-js">Please enable JavaScript to view this player.</p>
        </video>
    </div>

    <!-- Rotated UI Wrapper -->
    <div id="player-wrap" style="{{ $screenStyle }} z-index: 10; pointer-events: none;">
        <div id="ui-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 15;">
            @if ($logoUrl)
                <img id="logo" src="{{ $logoUrl }}" alt="Logo">
            @else
                <img id="logo" src="" alt="Logo" style="display:none">
            @endif
        </div>

        {{-- Image Slide Container (behind schedule overlay) --}}
        <div id="image-slide-container"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 20; display: none; overflow: hidden;">
            <img id="image-slide-img" src="" alt=""
                style="width: 100%; height: 100%; object-fit: contain; display: block;">
        </div>

        {{-- Event Overlay --}}
        <div id="event-overlay">
            <h1 id="overlay-location" class="overlay-location">{{ $overlay['location'] }}</h1>

            <div class="overlay-main-box">
                <div id="overlay-subtitle" class="overlay-subtitle">{{ $overlay['subtitle'] }}</div>
                <div id="overlay-title" class="overlay-title">{{ $overlay['title'] }}</div>

                <div class="overlay-details-grid">
                    <div class="overlay-detail-card">
                        <svg class="overlay-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="overlay-detail-content">
                            <div class="overlay-detail-label">Waktu</div>
                            <div id="overlay-time" class="overlay-detail-value">{{ $overlay['time'] }}</div>
                        </div>
                    </div>

                    <div class="overlay-detail-card">
                        <svg class="overlay-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div class="overlay-detail-content">
                            <div class="overlay-detail-label">Penyelenggara</div>
                            <div id="overlay-organizer" class="overlay-detail-value text-base whitespace-pre-line">
                                {{ $overlay['organizer'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="ticker-glass">
            <div id="ticker-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span id="ticker-clock"></span>
            </div>
            <div id="ticker-bar">
                <span id="ticker-text">{{ $runningText }}</span>
            </div>
        </div>
    </div>

    <script>
        window.PLAYER_INITIAL = {
            runningText: @json($runningText),
            logoUrl: @json($logoUrl),
            overlay: @json($overlay),
            imageSlides: @json($imageSlides),
            channelSlug: @json($channel->slug ?? 'main')
        };

        // Bulletproof dynamic resizer to bypass old webview vw/vh engine bugs
        function resizeLegacyWrapper() {
            var wrap = document.getElementById('player-wrap');
            var isPortrait = {{ $screenOrientation === 'portrait' ? 'true' : 'false' }};
            if (isPortrait) {
                wrap.style.width = window.innerHeight + 'px';
                wrap.style.height = window.innerWidth + 'px';
            } else {
                wrap.style.width = window.innerWidth + 'px';
                wrap.style.height = window.innerHeight + 'px';
            }
        }
        window.addEventListener('resize', resizeLegacyWrapper);
        window.addEventListener('orientationchange', resizeLegacyWrapper);
        document.addEventListener('DOMContentLoaded', resizeLegacyWrapper);
        // Execute immediately in case DOM is already parsed
        resizeLegacyWrapper();

        // Auto-refresh every 12 hours for TV signage (picks up playlist/settings updates)
        // Waits for the current video to finish before reloading to avoid mid-playback interruption.
        (function () {
            var REFRESH_INTERVAL = 12 * 60 * 60 * 1000; // 12 hours in ms
            var reloadPending = false;

            function doReload() {
                window.location.reload();
            }

            function scheduleReload() {
                reloadPending = true;
                // Try to wait for the video to end naturally (up to 60 s fallback)
                var video = document.getElementById('my-video');
                var fallback = setTimeout(doReload, 60000);
                if (video) {
                    var onVideoEnd = function () {
                        clearTimeout(fallback);
                        video.removeEventListener('ended', onVideoEnd);
                        video.removeEventListener('error', onVideoEnd);
                        doReload();
                    };
                    video.addEventListener('ended', onVideoEnd);
                    video.addEventListener('error', onVideoEnd);
                } else {
                    clearTimeout(fallback);
                    doReload();
                }
            }

            setTimeout(scheduleReload, REFRESH_INTERVAL);
        }());
    </script>
    {{-- Legacy fallback for Smart TV browsers that don't support ES modules --}}
    {!! App\Support\ViteLegacy::scripts(['resources/js/player.js']) !!}
</body>

</html>
