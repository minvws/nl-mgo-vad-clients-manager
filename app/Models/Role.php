<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role as RoleEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder<static>|Role newModelQuery()
 * @method static Builder<static>|Role newQuery()
 * @method static Builder<static>|Role query()
 * @method static Builder<static>|Role whereName($value)
 * @property RoleEnum $name
 * @property bool $view_all_stages
 *
 * @property-read UserRole $pivot
 * @mixin Eloquent
 */
class Role extends Model
{
    /**
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $primaryKey = 'name';

    /**
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $keyType = 'string';

    /**
     * @var bool
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    public $incrementing = false;

    /**
     * @var bool
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return array<string, class-string>
     */
    protected function casts(): array
    {
        return [
            'name' => RoleEnum::class,
        ];
    }
}
