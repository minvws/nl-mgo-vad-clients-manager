<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\AppException;
use App\Support\Environment;
use Illuminate\Foundation\Vite;
use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Basic;
use Spatie\Csp\Policies\Policy;
use Spatie\Csp\Scheme;

use function is_file;

class CspPolicy extends Basic
{
    public function __construct(
        private readonly Environment $environment,
        private readonly Vite $vite,
    ) {
    }

    /**
     * @throws AppException
     */
    public function configure(): Policy
    {
        $hosts = [Keyword::SELF];

        if ($this->environment->isDevelopmentOrTesting() && is_file($this->vite->hotFile())) {
            // Allow to connect to and fetch assets from the Vite development server
            $hosts[] = 'http://127.0.0.1:5173';
            $hosts[] = 'ws://127.0.0.1:5173';
        }

        $cspPolicy = $this
            ->addDirective(Directive::BASE, [Keyword::NONE])
            ->addDirective(Directive::BLOCK_ALL_MIXED_CONTENT, [])
            ->addDirective(Directive::CONNECT, [...$hosts, Scheme::DATA])
            ->addDirective(Directive::DEFAULT, [...$hosts, Scheme::DATA])
            ->addDirective(Directive::FONT, [...$hosts, Scheme::DATA])
            ->addDirective(Directive::FORM_ACTION, $hosts)
            ->addDirective(Directive::FRAME, $hosts)
            ->addDirective(Directive::FRAME_ANCESTORS, $hosts)
            ->addDirective(Directive::IMG, [...$hosts, Scheme::DATA, Scheme::BLOB])
            ->addDirective(Directive::MEDIA, $hosts)
            ->addDirective(Directive::OBJECT, [Keyword::NONE])
            ->addDirective(
                Directive::SCRIPT,
                $this->environment->isDevelopmentOrTesting() ? [...$hosts, Keyword::UNSAFE_EVAL] : $hosts,
            )
            ->addNonceForDirective(Directive::SCRIPT)
            ->addDirective(Directive::STYLE, $hosts)
            ->addNonceForDirective(Directive::STYLE)
            ->addDirective(Directive::WORKER, $hosts);

        if (!$this->environment->isDevelopmentOrTesting()) {
            $cspPolicy->addDirective(Directive::UPGRADE_INSECURE_REQUESTS, []);
        }

        return $cspPolicy;
    }
}
