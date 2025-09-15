<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\ClientObserver;
use Database\Factories\ClientFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @method static ClientFactory factory($count = null, $state = [])
 * @method static Builder<static>|Client whereActive($value)
 * @method static Builder<static>|Client whereCreatedAt($value)
 * @method static Builder<static>|Client whereFqdn($value)
 * @method static Builder<static>|Client whereId($value)
 * @method static Builder<static>|Client whereOrganisationId($value)
 * @method static Builder<static>|Client whereRedirectUris($value)
 * @method static Builder<static>|Client whereUpdatedAt($value)
 * @property string $id
 * @property string $organisation_id
 * @property array $redirect_uris
 * @property string $fqdn
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Organisation $organisation
 * @mixin Eloquent
 */

#[ObservedBy([ClientObserver::class])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [ //phpcs:ignore
        'id',
        'organisation_id',
        'redirect_uris',
        'fqdn',
        'active',
        'created_at',
        'updated_at',
    ];

    /** @var array<string, string> */
    protected $casts = [ //phpcs:ignore 
        'redirect_uris' => 'json',
    ];

    /**
     * @return BelongsTo<Organisation, $this>
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
