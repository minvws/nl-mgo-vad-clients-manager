<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Requests\ProfileUpdateRequest;
use App\Support\Auth;
use App\Support\I18n;
use App\Support\SessionHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use function view;

class ProfileController extends Controller
{
    public function __construct(
        private readonly SessionHelper $sessionHelper,
    ) {
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update($request->getValidatedAttributes());

        Session::regenerate();
        $this->sessionHelper->invalidateUser($user->id);

        return Redirect::route('profile.edit')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: I18n::trans('profile.flash.updated'),
            ));
    }
}
