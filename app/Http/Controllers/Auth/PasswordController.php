<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordUpdateRequest;
use App\Support\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use function back;

class PasswordController extends Controller
{
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Session::regenerate();

        return back()
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: 'user.flash.password_updated',
            ));
    }
}
