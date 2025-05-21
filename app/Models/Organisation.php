<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrganisationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @method static OrganisationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Organisation whereCocNumber($value)
 * @method static Builder<static>|Organisation whereCreatedAt($value)
 * @method static Builder<static>|Organisation whereId($value)
 * @method static Builder<static>|Organisation whereMainContactEmail($value)
 * @method static Builder<static>|Organisation whereMainContactName($value)
 * @method static Builder<static>|Organisation whereName($value)
 * @method static Builder<static>|Organisation whereNotes($value)
 * @method static Builder<static>|Organisation whereUpdatedAt($value)
 * @property string $id
 * @property string $main_contact_email
 * @property string $main_contact_name
 * @property string $name
 * @property string $coc_number
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin Eloquent
 */
class Organisation extends Model
{
    /** @use HasFactory<OrganisationFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [ //phpcs:ignore
        'id',
        'main_contact_email',
        'main_contact_name',
        'name',
        'coc_number',
        'notes',
        'created_at',
        'updated_at',
    ];
}
