<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Auth;
use App\Support\I18n;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function abort;
use function request;

class Requires2FA
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        if ($user->two_factor_secret !== null) {
            return $next($request);
        }

        Log::alert('User without 2FA attempting usage', [
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
        ]);

        abort(403, I18n::trans('No 2FA options available for user'));
    }
}
