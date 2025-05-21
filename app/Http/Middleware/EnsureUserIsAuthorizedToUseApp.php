<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Auth;
use App\Support\I18n;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

use function abort;
use function redirect;
use function route;

class EnsureUserIsAuthorizedToUseApp
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::userIfAuthenticated();

        if ($user === null || !$user->isRegisteredAnd2FaConfirmed()) {
            abort(401, 'Your account does not exist or is not activated.');
        }

        if (!$user->active) {
            AuthFacade::logout();

            Session::invalidate();
            Session::regenerateToken();

            return redirect(route('login'))->withErrors([
                'email' => I18n::trans('authentication.inactive'),
            ]);
        }

        return $next($request);
    }
}
