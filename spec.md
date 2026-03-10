# Hospital TV Broadcast System — Project Specification

## Overview

A Laravel 12 web application that allows a hospital admin to control video playback (play, pause, seek, switch video, loop) and broadcast the state in real-time to all TV displays connected on the local hospital network. TV displays open a dedicated player page (no login required) that syncs automatically with the admin's controls.

---

## Server Environment

| Item            | Value            |
| --------------- | ---------------- |
| Server IP       | `10.10.10.10`    |
| OS              | Ubuntu 24.04     |
| Web Server      | Nginx            |
| PHP             | 8.4 (Ondrej PPA) |
| Database        | MariaDB          |
| Laravel Version | 12.x (latest)    |
| Node.js         | 20.x LTS         |
| Package Manager | Composer + npm   |

---

## Tech Stack

| Layer                 | Technology                                |
| --------------------- | ----------------------------------------- |
| Backend Framework     | Laravel 12                                |
| Real-time / WebSocket | Laravel Reverb (official, self-hosted)    |
| WebSocket Client      | Laravel Echo + pusher-js                  |
| Frontend Build        | Vite (bundled with Laravel)               |
| Video Player          | Video.js (wide browser compatibility)     |
| Styling               | Tailwind CSS                              |
| Auth                  | Laravel Fortify (headless, session-based) |
| Storage               | Laravel local disk (`storage/app/public`) |

---

## Application Modules

### 1. Authentication (Admin Only)

- Laravel Fortify for login/logout (headless — provides routes and logic only)
- Custom login view at `resources/views/auth/login.blade.php` (Blade + Tailwind)
- Fortify handles: `POST /login`, `POST /logout`, CSRF protection, session management
- Only authenticated admins can access the admin panel (protected via `auth` middleware)
- Single admin user (seeded via DatabaseSeeder)
- Player page is **fully public** — no auth required
- No registration, password reset, or email verification needed — disable unused Fortify features in `config/fortify.php`

---

### 2. Admin Panel

#### 2a. Video Management

- Upload MP4 video files to `storage/app/public/videos/`
- Store video metadata in the `videos` database table:
    - `id`
    - `title` — display name
    - `filename` — stored filename
    - `path` — full storage path
    - `duration` — video duration in seconds (extracted via FFprobe or set manually)
    - `order` — playlist order (integer)
    - `created_at`, `updated_at`
- List all uploaded videos
- Delete a video (removes file + DB record)
- Reorder videos (drag-and-drop or up/down buttons) for playlist sequence

> **Video Format Requirement:** Only MP4 (H.264 video codec + AAC audio codec) is accepted. Other formats must be converted with FFmpeg before upload. Validate MIME type on upload (`video/mp4`).

#### 2b. Playback Control

Controls that the admin can operate, all of which are broadcast in real-time to all connected TVs:

| Control        | Description                                       |
| -------------- | ------------------------------------------------- |
| Play           | Start playback                                    |
| Pause          | Pause playback                                    |
| Seek           | Jump to a specific timestamp (seconds)            |
| Next Video     | Switch to next video in playlist                  |
| Previous Video | Switch to previous video in playlist              |
| Select Video   | Jump directly to a specific video in the playlist |
| Loop Mode      | Toggle between: `none`, `single`, `playlist`      |

#### 2c. Running Text Management

- Input field for the running text (ticker) message
- "Update" button to push new running text to all TVs immediately via broadcast
- Text is persisted in a `settings` table so it survives server restarts

#### 2d. Logo Management

- Upload hospital logo image (PNG or SVG recommended)
- Stored at `storage/app/public/logo/`
- Displayed on top-left corner of every TV player page
- Persisted in `settings` table
- Updating logo broadcasts the new logo URL to all TVs immediately

---

### 3. Player Page (TV Display)

- Route: `GET /player`
- No authentication required — fully public
- Designed to be opened on Smart TVs, PC browsers, and older devices
- Fullscreen layout (no scrollbar, no browser UI expected)

#### Layout

```
┌─────────────────────────────────────────────┐
│ [LOGO — top left]                           │
│                                             │
│                                             │
│              VIDEO PLAYER                   │
│           (fullscreen, centered)            │
│                                             │
│                                             │
├─────────────────────────────────────────────┤
│ ◄  Running text scrolling right to left   ► │
└─────────────────────────────────────────────┘
```

#### Behavior

- On page load, the player fetches **current broadcast state** from the server via HTTP (`GET /api/player/state`) before connecting to WebSocket
- State includes: current video URL, current playback position (seconds), is_playing, loop_mode, running_text, logo_url
- Player seeks to the current position and respects `is_playing` immediately
- Player then subscribes to the Reverb channel and listens for events
- All controls from admin are reflected immediately on all connected TVs

#### Loop Behavior

| Mode       | Behavior                                                |
| ---------- | ------------------------------------------------------- |
| `none`     | Stop after current video ends                           |
| `single`   | Repeat current video indefinitely                       |
| `playlist` | Play all videos in order, loop back to first after last |

#### Compatibility

- Use **Video.js** for maximum browser compatibility
- Use standard HTML5 `<video>` fallback
- Avoid CSS Grid — use Flexbox only
- JS transpiled via Vite for older browser support
- Laravel Echo falls back to **long polling** automatically if WebSocket is not supported by the browser

---

## Real-time Broadcasting (Laravel Reverb)

### Channel

- Channel type: **Public channel** (no auth needed for player page)
- Channel name: `tv-broadcast`

### Events Broadcast

All events are broadcast on the `tv-broadcast` channel.

| Event Class          | Trigger                | Payload                             |
| -------------------- | ---------------------- | ----------------------------------- |
| `VideoPlayed`        | Admin clicks Play      | `{ video_id, position, timestamp }` |
| `VideoPaused`        | Admin clicks Pause     | `{ position, timestamp }`           |
| `VideoSeeked`        | Admin seeks            | `{ position, timestamp }`           |
| `VideoChanged`       | Admin switches video   | `{ video_id, position, loop_mode }` |
| `LoopModeChanged`    | Admin changes loop     | `{ loop_mode }`                     |
| `RunningTextUpdated` | Admin updates ticker   | `{ text }`                          |
| `LogoUpdated`        | Admin uploads new logo | `{ logo_url }`                      |

### State Persistence

A `broadcast_state` table (or use Laravel Cache) stores the current live state so new TVs joining mid-session can sync:

```
broadcast_state
├── current_video_id
├── current_position (seconds, float)
├── is_playing (boolean)
├── loop_mode (enum: none, single, playlist)
├── started_at (timestamp — to calculate live position offset)
└── updated_at
```

When a TV opens `/player`, it calls `GET /api/player/state` which returns the above state plus video URL, running text, and logo URL. The player calculates the actual current position as:

```
actual_position = current_position + (now - started_at)   // if is_playing = true
actual_position = current_position                         // if is_playing = false
```

---

## Database Schema

### `users`

Standard Laravel users table. Only admin accounts (no public registration).

### `videos`

```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
title           VARCHAR(255)
filename        VARCHAR(255)
path            VARCHAR(500)
duration        INT UNSIGNED DEFAULT 0   -- seconds
order           INT UNSIGNED DEFAULT 0
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### `settings`

Key-value store for persistent app settings.

```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
key             VARCHAR(100) UNIQUE
value           TEXT NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

Seeded keys:

- `running_text` → default: `"Selamat datang di Rumah Sakit"`
- `logo_path` → default: `null`

### `broadcast_state`

```sql
id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
current_video_id BIGINT UNSIGNED NULLABLE
current_position DECIMAL(10,3) DEFAULT 0
is_playing      TINYINT(1) DEFAULT 0
loop_mode       ENUM('none','single','playlist') DEFAULT 'none'
started_at      TIMESTAMP NULLABLE
updated_at      TIMESTAMP
```

Single row — always update, never insert new rows after seeding.

---

## API Endpoints

| Method   | URI                            | Auth     | Description                        |
| -------- | ------------------------------ | -------- | ---------------------------------- |
| `GET`    | `/login`                       | None     | Admin login page (custom view)     |
| `POST`   | `/login`                       | None     | Fortify login handler              |
| `POST`   | `/logout`                      | Required | Fortify logout handler             |
| `GET`    | `/player`                      | None     | TV player page                     |
| `GET`    | `/api/player/state`            | None     | Returns current broadcast state    |
| `GET`    | `/admin`                       | Required | Admin dashboard / playback control |
| `GET`    | `/admin/videos`                | Required | Video list                         |
| `POST`   | `/admin/videos`                | Required | Upload new video                   |
| `DELETE` | `/admin/videos/{id}`           | Required | Delete video                       |
| `PATCH`  | `/admin/videos/reorder`        | Required | Update playlist order              |
| `POST`   | `/admin/playback/play`         | Required | Broadcast play                     |
| `POST`   | `/admin/playback/pause`        | Required | Broadcast pause                    |
| `POST`   | `/admin/playback/seek`         | Required | Broadcast seek                     |
| `POST`   | `/admin/playback/change`       | Required | Broadcast video change             |
| `POST`   | `/admin/playback/loop`         | Required | Broadcast loop mode change         |
| `POST`   | `/admin/settings/running-text` | Required | Update & broadcast running text    |
| `POST`   | `/admin/settings/logo`         | Required | Upload & broadcast new logo        |

---

## Laravel Reverb Configuration

In `.env`:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=hospital-tv
REVERB_APP_KEY=hospital-tv-key
REVERB_APP_SECRET=hospital-tv-secret
REVERB_HOST=10.10.10.10
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Run Reverb server (keep alive with Supervisor or PM2):

```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

---

## Queue Configuration

Broadcasting events requires the queue worker to be running:

```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:work
```

Keep alive with Supervisor. Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/hospital-tv/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/hospital-tv/storage/logs/worker.log
```

```ini
[program:laravel-reverb]
process_name=%(program_name)s
command=php /var/www/hospital-tv/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/hospital-tv/storage/logs/reverb.log
```

---

## Nginx Configuration

```nginx
server {
    listen 80;
    server_name 10.10.10.10;
    root /var/www/hospital-tv/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

> The `/app` location block proxies WebSocket connections from Reverb through Nginx — this is required.

---

## File & Folder Structure (relevant parts)

```
/var/www/hospital-tv/
├── app/
│   ├── Events/
│   │   ├── VideoPlayed.php
│   │   ├── VideoPaused.php
│   │   ├── VideoSeeked.php
│   │   ├── VideoChanged.php
│   │   ├── LoopModeChanged.php
│   │   ├── RunningTextUpdated.php
│   │   └── LogoUpdated.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── VideoController.php
│   │   │   │   ├── PlaybackController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── PlayerController.php
│   │   │   └── Api/PlayerStateController.php
│   └── Models/
│       ├── Video.php
│       ├── Setting.php
│       └── BroadcastState.php
├── resources/
│   ├── js/
│   │   ├── player.js       -- Video.js + Echo listener for TV page
│   │   └── admin.js        -- Admin controls + Echo for confirmation
│   └── views/
│       ├── auth/
│       │   └── login.blade.php     -- custom Fortify login view
│       ├── player.blade.php
│       └── admin/
│           ├── dashboard.blade.php
│           ├── videos.blade.php
│           └── settings.blade.php
├── routes/
│   ├── web.php
│   ├── api.php
│   └── channels.php
└── storage/app/public/
    ├── videos/             -- uploaded MP4 files
    └── logo/               -- uploaded logo file
```

---

## Installation Steps (for Claude Code)

```bash
# 1. Create Laravel project
composer create-project laravel/laravel hospital-tv
cd hospital-tv

# 2. Install Reverb
php artisan install:broadcasting
# Select: Reverb

# 3. Install Fortify (auth)
composer require laravel/fortify
php artisan fortify:install
# In config/fortify.php: disable registration, password reset, email verification
# Create resources/views/auth/login.blade.php (custom login view)

# 4. Install Video.js via npm
npm install video.js
npm install && npm run build

# 5. Configure .env (DB, Reverb, storage)

# 6. Run migrations + seeders
php artisan migrate --seed

# 7. Link storage
php artisan storage:link

# 8. Start Reverb (dev)
php artisan reverb:start --host=0.0.0.0 --port=8080

# 9. Start queue worker (dev)
php artisan queue:work

# 10. Start Vite (dev) or build for production
npm run dev
# or
npm run build
```

---

## Seed Data

`DatabaseSeeder.php` should seed:

1. One admin user:
    - Email: `admin@hospital.local`
    - Password: `password` (hashed)

2. One `broadcast_state` row with defaults

3. `settings` rows:
    - `running_text` = `"Selamat datang di Rumah Sakit"`
    - `logo_path` = `null`

---

## Notes & Constraints

- All video files must be **MP4 with H.264 + AAC** for maximum TV/browser compatibility
- Do **not** use CSS Grid in player page — use Flexbox only
- Running text uses CSS `animation: marquee` (pure CSS scroll, no JS dependency)
- Logo and running text must survive page refresh (loaded from DB/API on each page load)
- Player page must work even if WebSocket connection drops — it should attempt to reconnect automatically (Laravel Echo handles this natively)
- Admin panel does not need to be mobile responsive — desktop only is fine
- Player page should be **fullscreen-friendly** — no overflow, no scrollbars
- Max upload file size: configure `upload_max_filesize` and `post_max_size` in PHP to at least `500M`
