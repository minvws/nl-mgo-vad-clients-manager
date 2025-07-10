<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static Builder<static>|UserRole newModelQuery()
 * @method static Builder<static>|UserRole newQuery()
 * @method static Builder<static>|UserRole query()
 *
 * @mixin Eloquent
 */
class UserRole extends Pivot
{
    protected $table = 'role_user';
}
