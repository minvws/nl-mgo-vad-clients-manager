<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use App\Notifications\Auth\UserRegistered;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

use function encrypt;
use function hash;
use function sprintf;

class UserAdminCreate extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'user:create-admin {email} {name} {--sendMail}';

    /**
     * The console command description.
     *
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Create the initial admin user';

    /**
     * Execute the console command.
     */
    public function handle(
        TwoFactorAuthenticationProvider $authProvider,
        UserService $userService,
    ): int
    {
        DB::transaction(function () use ($authProvider, $userService): void {
            $registrationToken = Str::random(32);

            $user = User::updateOrCreate(
                [
                    "email" => $this->argument('email'),
                ],
                [
                    "name" => $this->argument('name'),
                    "password" => Hash::make(Str::random(32)),
                    'registration_token' => hash('sha256', $registrationToken),
                    "active" => true,
                ],
            );

            // Add the admin role to the user
            $user->attachRole(Role::UserAdmin);

            $secret = $authProvider->generateSecretKey();

            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
            ]);
            $user->save();

            $registerUrl = $userService->generateRegistrationUrl($registrationToken);

            if ($this->option('sendMail')) {
                $user->notify(new UserRegistered($registerUrl));

                $this->info('User admin created and email sent.');
                $this->newLine();

                return;
            }

            $this->info(
                sprintf(
                // phpcs:ignore
                    "User admin %s created. Please share the following URL to let the user make his registration complete:",
                    $user->email,
                ),
            );
            $this->info($registerUrl);
            $this->newLine();
        });

        return 0;
    }
}
