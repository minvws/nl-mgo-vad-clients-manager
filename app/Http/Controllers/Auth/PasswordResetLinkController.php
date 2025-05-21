<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use function redirect;
use function route;
use function view;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(
            $request->only('email'),
        );

        return redirect(route('login'))
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: 'authentication.forgot_password.mail_sent',
            ));
    }
}
