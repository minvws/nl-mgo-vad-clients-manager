<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Session;
use Webmozart\Assert\Assert;

class Auth
{
    public static function user(): User
    {
        $user = AuthFacade::user();

        Assert::isInstanceOf($user, User::class);

        return $user;
    }

    public static function userIfAuthenticated(): ?User
    {
        $user = AuthFacade::user();

        Assert::nullOrIsInstanceOf($user, User::class);

        return $user;
    }

    public static function logout(): void
    {
        AuthFacade::logout();
        Session::invalidate();
        Session::regenerateToken();
    }
}
