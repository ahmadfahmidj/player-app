<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class ViteLegacy
{
    /**
     * Render nomodule script tags for legacy browser fallback.
     *
     * @param  array<string>  $entrypoints
     */
    public static function scripts(array $entrypoints): HtmlString
    {
        $manifestPath = public_path('build/manifest.json');

        if (! file_exists($manifestPath)) {
            return new HtmlString('');
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $html = '';

        // Polyfills must load first
        if (isset($manifest['vite/legacy-polyfills-legacy'])) {
            $polyfillFile = $manifest['vite/legacy-polyfills-legacy']['file'];
            $html .= '<script nomodule src="/build/'.$polyfillFile.'"></script>'."\n";
        }

        foreach ($entrypoints as $entry) {
            $legacyKey = self::legacyKey($entry);

            if (! isset($manifest[$legacyKey])) {
                continue;
            }

            $legacyEntry = $manifest[$legacyKey];

            // Load imports first
            foreach ($legacyEntry['imports'] ?? [] as $import) {
                if (isset($manifest[$import])) {
                    $html .= '<script nomodule src="/build/'.$manifest[$import]['file'].'"></script>'."\n";
                }
            }

            $html .= '<script nomodule src="/build/'.$legacyEntry['file'].'"></script>'."\n";
        }

        return new HtmlString($html);
    }

    private static function legacyKey(string $entry): string
    {
        // resources/js/player.js -> resources/js/player-legacy.js
        // resources/css/app.css -> resources/css/app-legacy.css
        $ext = pathinfo($entry, PATHINFO_EXTENSION);
        $base = substr($entry, 0, -(strlen($ext) + 1));

        return $base.'-legacy.'.$ext;
    }
}
