<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #logo {
            position: absolute;
            top: 2rem;
            left: 2rem;
            max-height: 80px;
            max-width: 220px;
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

        /* Glassmorphic Ticker */
        #ticker-glass {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1.25rem 0;
            z-index: 30;
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.7) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        /* Decorative glowing line top of ticker */
        #ticker-glass::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.8), rgba(236, 72, 153, 0.8), transparent);
        }

        #ticker-badge {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 2rem;
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            z-index: 35;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.3);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
            min-width: 180px;
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
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
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

            <video id="my-video" class="video-js vjs-big-play-centered" playsinline>
                <p class="vjs-no-js">Please enable JavaScript to view this player.</p>
            </video>
        </div>

        <div id="ticker-glass">
            <div id="ticker-badge">INFO TEKINI</div>
            <div id="ticker-bar">
                <span id="ticker-text">{{ $runningText }}</span>
            </div>
        </div>
    </div>

    <script>
        window.PLAYER_INITIAL = {
            runningText: @json($runningText),
            logoUrl: @json($logoUrl),
        };
    </script>
    {{-- Legacy fallback for Smart TV browsers that don't support ES modules --}}
    {!! App\Support\ViteLegacy::scripts(['resources/js/player.js']) !!}
</body>

</html>
