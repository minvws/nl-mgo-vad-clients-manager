<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function redirect;

class AuthenticatedSessionController extends Controller
{
    /**
     * Destroy an authenticated session.
     */
    public function destroy(): RedirectResponse
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerate();

        return redirect('/');
    }
}
