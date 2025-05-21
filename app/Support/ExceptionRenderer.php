<?php

declare(strict_types=1);

namespace App\Support;

use Exception;
use Illuminate\Foundation\Exceptions\Renderer\Renderer;
use Illuminate\Foundation\Vite;

use function app;
use function collect;
use function csp_nonce;
use function file_get_contents;
use function is_file;
use function sprintf;

class ExceptionRenderer extends Renderer
{
    public static function css(): string
    {
        $cspNonce = csp_nonce();

        return collect([
            ['styles.css', ['nonce' => $cspNonce]],
            ['light-mode.css', ['nonce' => $cspNonce, 'data-theme' => 'light']],
            ['dark-mode.css', ['nonce' => $cspNonce, 'data-theme' => 'dark']],
        ])->map(static function ($fileAndAttributes) {
            [$filename, $attributes] = $fileAndAttributes;

            $stringAttributes = collect($attributes)
                ->map(static fn($value, $attribute) => sprintf('%s="%s"', $attribute, $value))
                ->implode(' ');

            return sprintf('<style %s>%s</style>', $stringAttributes, file_get_contents(self::DIST . $filename));
        })->implode("\n");
    }

    /**
     * Get the renderer's JavaScript content.
     *
     * @throws Exception
     */
    public static function js(): string
    {
        $viteJsAutoRefresh = '';

        $vite = app(Vite::class);

        if (is_file($vite->hotFile())) {
            $viteJsAutoRefresh = $vite([]);
        }

        return sprintf(
            '<script nonce="%s">%s</script>%s',
            csp_nonce(),
            file_get_contents(self::DIST . 'scripts.js'),
            $viteJsAutoRefresh,
        );
    }
}
