<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Events\Logging\UserPasswordResetEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewPasswordByUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use MinVWS\Logging\Laravel\LogService;

use function abort_unless;
use function assert;
use function back;
use function decrypt;
use function encrypt;
use function event;
use function is_string;
use function now;
use function redirect;
use function trans;
use function view;

class NewPasswordController extends Controller
{
    public function __construct(
        private readonly TwoFactorAuthenticationProvider $provider,
        private readonly LogService $logger,
    ) {
    }

    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        // make sure we have the correct user to correct
        $user = Password::getUser($request->only('email'));

        $resetToken = $request->token;
        assert(is_string($resetToken));

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
     */
    public function store(NewPasswordByUserRequest $request): RedirectResponse
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request): void {
                $password = $request->password;
                $code = $request->two_factor_code;

                $secret = decrypt($user->two_factor_secret);
                assert(is_string($secret));

                if (empty($user->two_factor_secret) || !$this->provider->verify($secret, $code)) {
                    throw ValidationException::withMessages([
                        'two_factor_code' => [trans('The provided two factor authentication code was invalid.')],
                    ]);
                }

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'registered_at' => now(),
                    'two_factor_confirmed_at' => now(),
                    'registration_token' => null,
                ])->save();

                $this->logger->log((new UserPasswordResetEvent())
                    ->withActor($user)
                    ->withData([
                        'userId' => $user->id,
                    ]));

                event(new PasswordReset($user));
            },
        );

        assert(is_string($status));

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                    type: FlashNotificationTypeEnum::CONFIRMATION,
                    message: 'authentication.forgot_password.reset_success',
                ));
        }

        // @codeCoverageIgnoreStart
        // Currently we don't have test for the edge cases of invalid token etc.
        return back()
            ->withInput($request->only('email'))
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
        // @codeCoverageIgnoreEnd
    }
}
