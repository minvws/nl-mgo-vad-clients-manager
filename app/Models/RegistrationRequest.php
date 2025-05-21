<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RegistrationRequestFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @method static RegistrationRequestFactory factory($count = null, $state = [])
 * @method static Builder<static>|RegistrationRequest newModelQuery()
 * @method static Builder<static>|RegistrationRequest newQuery()
 * @method static Builder<static>|RegistrationRequest query()
 * @method static Builder<static>|RegistrationRequest whereClientFqdn($value)
 * @method static Builder<static>|RegistrationRequest whereClientRedirectUris($value)
 * @method static Builder<static>|RegistrationRequest whereCreatedAt($value)
 * @method static Builder<static>|RegistrationRequest whereId($value)
 * @method static Builder<static>|RegistrationRequest whereNotes($value)
 * @method static Builder<static>|RegistrationRequest whereOrganisationCocNumber($value)
 * @method static Builder<static>|RegistrationRequest whereOrganisationMainContactEmail($value)
 * @method static Builder<static>|RegistrationRequest whereOrganisationMainContactName($value)
 * @method static Builder<static>|RegistrationRequest whereOrganisationName($value)
 * @method static Builder<static>|RegistrationRequest whereUpdatedAt($value)
 * @property string $id
 * @property string $organisation_name
 * @property string $organisation_main_contact_email
 * @property string $organisation_main_contact_name
 * @property string $organisation_coc_number
 * @property string $client_redirect_uris
 * @property string $client_fqdn
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin Eloquent
 */
class RegistrationRequest extends Model
{
    /** @use HasFactory<RegistrationRequestFactory> */
    use HasFactory;
    use HasUuids;

    protected $fillable = [ //phpcs:ignore
        'id',
        'organisation_name',
        'organisation_main_contact_email',
        'organisation_main_contact_name',
        'organisation_coc_number',
        'client_redirect_uris',
        'client_fqdn',
        'notes',
    ];
}
