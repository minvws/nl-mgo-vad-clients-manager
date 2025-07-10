<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StorePasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use TypeError;

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
     * @throws TypeError
     */
    public function store(StorePasswordResetLinkRequest $request): RedirectResponse
    {
        $dto = $request->getValidatedDto();
        Password::sendResetLink(['email' => $dto->email]);

        return redirect(route('login'))
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: 'authentication.forgot_password.mail_sent',
            ));
    }
}
