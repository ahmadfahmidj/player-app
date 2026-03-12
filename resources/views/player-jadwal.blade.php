<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital TV - Jadwal</title>
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

        #player-wrap {
            position: relative;
            width: 100vw;
            height: 100vh;
        }

        #video-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
        }

        #logo {
            position: absolute;
            top: 50%;
            right: 1.5rem;
            transform: translateY(-50%) rotate(90deg);
            max-height: 70px;
            max-width: 200px;
            object-fit: contain;
            z-index: 40;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.5));
            transition: opacity 0.5s ease-in-out;
        }

        #my-video {
            width: 100%;
            height: 100%;
        }

        .video-js {
            width: 100% !important;
            height: 100% !important;
            background-color: transparent !important;
        }

        .video-js video {
            object-fit: cover;
            transform: rotate(90deg);
            transform-origin: center center;
            width: 100vh !important;
            height: 100vw !important;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: calc(-50vw);
            margin-left: calc(-50vh);
        }

        /* Glassmorphic Ticker - rotated for portrait on landscape screen */
        #ticker-glass {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 100%;
            width: auto;
            z-index: 30;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.7) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 10px 0 40px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            display: flex;
            flex-direction: row;
            align-items: stretch;
        }

        /* Decorative glowing line */
        #ticker-glass::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 1px;
            background: linear-gradient(180deg, transparent, rgba(56, 189, 248, 0.8), rgba(236, 72, 153, 0.8), transparent);
        }

        #ticker-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            padding: 1.5rem 0.6rem;
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white;
            font-weight: 800;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        #ticker-bar {
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            padding: 0 0.75rem;
        }

        #ticker-text {
            display: inline-block;
            padding-top: 100vh;
            animation: marquee-vertical 15s linear infinite;
            font-size: 1.1rem;
            font-weight: 600;
            color: #f8fafc;
            letter-spacing: 0.05em;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);
        }

        @keyframes marquee-vertical {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-100%);
            }
        }
    </style>
</head>

<body>
    <div id="player-wrap">
        <div id="video-container">
            @if ($logoUrl)
                <img id="logo" src="{{ $logoUrl }}" alt="Logo">
            @else
                <img id="logo" src="" alt="Logo" style="display:none">
            @endif

            <video id="my-video" class="video-js vjs-big-play-centered" playsinline autoplay muted>
                <p class="vjs-no-js">{{ __('Please enable JavaScript to view this player.') }}</p>
            </video>
        </div>

        <div id="ticker-glass">
            <div id="ticker-badge"></div>
            <div id="ticker-bar">
                <span id="ticker-text">{{ $runningText }}</span>
            </div>
        </div>
    </div>

    <script>
        window.PLAYER_INITIAL = {
            runningText: @json($runningText),
            logoUrl: @json($logoUrl),
            channelSlug: @json($channel->slug ?? 'main')
        };
    </script>
    {{-- Legacy fallback for Smart TV browsers that don't support ES modules --}}
    {!! App\Support\ViteLegacy::scripts(['resources/js/player.js']) !!}
</body>

</html>
