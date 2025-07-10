<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Role;
use App\Notifications\Auth\UserRegistered;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Webmozart\Assert\Assert;

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
        DB::transaction(function () use ($userService): void {
            $registrationToken = Str::random(32);

            $name = $this->argument('name');
            Assert::string($name);
            $email = $this->argument('email');
            Assert::string($email);

            $user = $userService->createUser(
                $name,
                $email,
                [Role::UserAdmin],
                $registrationToken,
            );

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
