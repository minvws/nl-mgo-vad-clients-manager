<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Support\Auth;
use App\Support\I18n;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EnsureUserIsActive
{
    /**
     * @throws ValidationException
     */
    public function handle(Request $request, callable $next): mixed
    {
        $user = Auth::userIfAuthenticated();

        if ($user && !$user->active) {
            throw ValidationException::withMessages([
                'email' => [
                    I18n::trans('authentication.inactive'),
                ],
            ]);
        }

        return $next($request);
    }
}
