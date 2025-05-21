<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Events\Logging\UserRegisteredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use MinVWS\Logging\Laravel\LogService;

use function abort_unless;
use function assert;
use function decrypt;
use function event;
use function hash;
use function is_string;
use function now;
use function trans;
use function view;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly TwoFactorAuthenticationProvider $provider,
        private readonly LogService $logger,
    ) {
    }

    /**
     * Display the registration view.
     */
    public function create(Request $request, string $token): View
    {
        $user = User::whereRegistrationToken(hash('sha256', $token))->firstOrFail();

        abort_unless($request->hasValidSignature() && !$user->isRegistered(), 401);

        return view('auth.register', ['user' => $user, 'token' => $token]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(UserRegisterRequest $request): RedirectResponse
    {
        $token = $request->session()->pull('registration_token') ?? $request->token;
        assert(is_string($token));

        /** @var User $user */
        $user = User::whereRegistrationToken(hash('sha256', $token))->first();
        assert($user instanceof User);

        abort_unless(!$user->isRegistered(), 401);

        if (empty($user->two_factor_secret)) {
            Session::flash(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.register_error',
            ));

            throw ValidationException::withMessages([
                'two_factor_code' => [trans('The provided two factor authentication code was invalid.')],
            ]);
        }

        $code = $request->two_factor_code;
        $secret = decrypt($user->two_factor_secret);
        assert(is_string($secret));

        if (!$this->provider->verify($secret, $code)) {
            Session::flash(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.register_error',
            ));

            throw ValidationException::withMessages([
                'two_factor_code' => [trans('The provided two factor authentication code was invalid.')],
            ]);
        }

        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'two_factor_confirmed_at' => now(),
        ]);

        $user->markAsRegistered();

        $this->logger->log((new UserRegisteredEvent())
            ->withActor($user)
            ->withData([
                'userId' => $user->id,
            ]));

        event(new Registered($user));

        Auth::login($user);

        return Redirect::route('dashboard');
    }
}
