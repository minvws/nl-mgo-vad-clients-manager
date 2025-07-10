<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Requests\Profile\TwoFactorResetRequest;
use App\Models\User;
use App\Support\Auth;
use App\Support\I18n;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Log\Logger;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use TypeError;
use Webmozart\Assert\Assert;

use function decrypt;
use function encrypt;
use function now;
use function redirect;
use function request;
use function view;

class TwoFactorAuthenticationController extends Controller
{
    public function __construct(
        private readonly TwoFactorAuthenticationProvider $twoFactorAuthenticationProvider,
        private readonly Logger $logger,
    ) {
    }

    public function create(): View
    {
        $secretKey = $this->twoFactorAuthenticationProvider->generateSecretKey();
        $encryptedSecret = encrypt($secretKey);

        //We do this so that we don't have to alter the users' secret just yet, but we can generate a QR with a new secret
        $user = new User();
        //Type cast is needed because the string() method returns a Stringable which is not compatible with the User model
        $encryptedSecret = (string) request()->string('encrypted_secret', $encryptedSecret);
        $user->two_factor_secret = $encryptedSecret;
        $qrCode = $user->twoFactorQrCodeSvgWithAria();

        return view('profile.2fa.create', [
            'qrCode' => $qrCode,
            'encryptedSecret' => $encryptedSecret,
        ]);
    }

    /**
     * @throws TypeError
     */
    public function update(TwoFactorResetRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $dto = $request->getValidatedDto();
        $encryptedSecret = $dto->encrypted_secret;
        Assert::stringNotEmpty($encryptedSecret);

        try {
            $decryptedSecret = decrypt($encryptedSecret);
            Assert::stringNotEmpty($decryptedSecret);
        } catch (DecryptException $e) {
            $this->logger->error('User tried to update 2FA with non-decryptable secret key.', [
                'userId' => $user->id,
                'payload' => $encryptedSecret,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('profile.2fa.reset', ['encrypted_secret' => $encryptedSecret])
                ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                    type: FlashNotificationTypeEnum::ERROR,
                    message: I18n::trans('profile.2fa.non_decryptable_secret'),
                ));
        }

        if (!$this->twoFactorAuthenticationProvider->verify($decryptedSecret, $dto->code)) {
            return redirect()
                ->route('profile.2fa.reset', ['encrypted_secret' => $encryptedSecret])
                ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                    type: FlashNotificationTypeEnum::ERROR,
                    message: I18n::trans('profile.2fa.invalid'),
                ));
        }

        //Explicitly set the secret key to prevent allowing it in mass assignments
        $user->two_factor_secret = $encryptedSecret;
        $user->two_factor_confirmed_at = now();
        $user->save();

        Auth::logout();

        return redirect()->route('login')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: I18n::trans('profile.2fa.reset_success'),
            ));
    }
}
