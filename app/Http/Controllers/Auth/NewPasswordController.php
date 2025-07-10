<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateNewPasswordRequest;
use App\Http\Requests\User\StorePasswordRequest;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use TypeError;

use function abort_unless;
use function assert;
use function back;
use function encrypt;
use function redirect;
use function view;

class NewPasswordController extends Controller
{
    public function __construct(
        private readonly TwoFactorAuthenticationProvider $provider,
        private readonly PasswordResetService $passwordResetService,
    ) {
    }

    /**
     * Display the password reset view.
     *
     * @throws TypeError
     */
    public function create(CreateNewPasswordRequest $request, string $resetToken): View
    {
        $dto = $request->getValidatedDto();
        $user = Password::getUser(['email' => $dto->email]);

        abort_unless($request->hasValidSignature() && ($user && Password::tokenExists($user, $resetToken)), 401);
        assert($user instanceof User);

        // as we have a valid user and token, we now force the reset by generating new password and two factor secret
        $secretKey = $this->provider->generateSecretKey();
        $user->forceFill([
            'password' => Hash::make(Str::random(32)),
            'two_factor_secret' => encrypt($secretKey),
        ]);

        $user->save();
        $user->refresh();

        return view('auth.reset-password', [
            'token' => $resetToken,
            'email' => $user->email,
            'twoFactorQrCodeUrl' => $user->twoFactorQrCodeUrl(),
            'twoFactorQrCodeSvgWithAria' => $user->twoFactorQrCodeSvgWithAria(),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws TypeError
     */
    public function store(StorePasswordRequest $request): RedirectResponse
    {
        $dto = $request->getValidatedDto();

        try {
            $status = $this->passwordResetService->resetPassword($dto);
        } catch (ValidationException $e) {
            return back()
                ->withInput(['email' => $dto->email])
                ->withErrors($e->errors())
                ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                    type: FlashNotificationTypeEnum::ERROR,
                    message: 'authentication.forgot_password.reset_error',
                ));
        }

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                    type: FlashNotificationTypeEnum::CONFIRMATION,
                    message: 'authentication.forgot_password.reset_success',
                ));
        }

        return back()
            ->withInput(['email' => $dto->email])
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
    }
}
