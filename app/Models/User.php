<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role as RoleEnum;
use App\Notifications\Auth\UserPasswordReset;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use MinVWS\AuditLogger\Contracts\LoggableUser;
use SensitiveParameter;

use function in_array;
use function is_array;
use function now;
use function sprintf;
use function str_replace;
use function trans;

/**
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User filterByNameOrEmail(?string $filter = null)
 * @method static Builder<static>|User onlyTrashed()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDeletedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRegisteredAt($value)
 * @method static Builder<static>|User whereRegistrationToken($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static Builder<static>|User whereTwoFactorSecret($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withTrashed()
 * @method static Builder<static>|User withoutTrashed()
 * @property string $two_factor_secret
 * @property string $email
 * @property string $password
 * @property string $id
 * @property string $name
 * @property bool $active
 * @property Carbon|null $registered_at
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $registration_token
 * @property Collection<string, Role> $roles
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read UserRole|null $pivot
 * @property-read int|null $roles_count
 * @mixin Eloquent
 */
class User extends Authenticatable implements LoggableUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasUuids;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'registered_at',
        'registration_token',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $hidden = [
        'password',
        'remember_token',
        'registered_at',
        'registration_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'registered_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->using(UserRole::class);
    }

    public function roleList(): string
    {
        return $this->roles
            ->map(static fn(Role $role) => trans('role.' . $role->name->value))
            ->implode(', ');
    }

    public function attachRole(RoleEnum $role): void
    {
        if ($this->roles()->where('name', $role->value)->exists()) {
            return;
        }

        $this->roles()->attach($role->value);
    }

    public function isUserAdministrator(): bool
    {
        return $this->hasRole(RoleEnum::UserAdmin);
    }

    public function isRegistered(): bool
    {
        return $this->registration_token === null && $this->registered_at !== null;
    }

    public function isRegisteredAnd2FaConfirmed(): bool
    {
        return $this->isRegistered() && $this->two_factor_confirmed_at !== null;
    }

    public function markAsRegistered(): void
    {
        $this->registered_at = now();
        $this->registration_token = null;
        $this->two_factor_confirmed_at = now();

        $this->save();
    }

    /**
     * @param Builder<User> $query
     *
     * @return Builder<User>
     */
    public function scopeFilterByNameOrEmail(Builder $query, ?string $filter = null): Builder
    {
        return $query->when(
            $filter,
            static fn() => $query->where(static function (Builder $query) use ($filter): void {
                $query
                    ->where('name', 'like', sprintf("%%%s%%", $filter))
                    ->orWhere('email', 'like', sprintf("%%%s%%", $filter));
            }),
        );
    }

    public function twoFactorQrCodeSvgWithAria(): string
    {
        $svgTag = $this->twoFactorQrCodeSvg();
        return str_replace('<svg ', '<svg role="img" focusable="false" aria-label="QR-code" ', $svgTag);
    }

    /**
     * @param RoleEnum|array<RoleEnum> $role
     */
    public function hasRole(RoleEnum|array $role): bool
    {
        $roles = is_array($role) ? $role : [$role];

        return $this->roles
            ->filter(static fn(Role $userRole) => in_array($userRole->name, $roles, true))
            ->isNotEmpty();
    }

    public function getAuditId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<mixed|string>
     */
    public function getRoles(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $token
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function sendPasswordResetNotification(#[SensitiveParameter] $token): void
    {
        $this->notify(new UserPasswordReset($token));
    }
}
