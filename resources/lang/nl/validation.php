<?php

declare(strict_types=1);

return [
    'accepted' => ':attribute moet worden geaccepteerd.',
    'accepted_if' => ':attribute moet worden geaccepteerd als :other :value is.',
    'active_url' => ':attribute is geen geldige URL.',
    'after' => ':attribute moet een datum na :date zijn.',
    'after_or_equal' => ':attribute moet een datum na of gelijk aan :date zijn.',
    'alpha' => ':attribute mag alleen letters bevatten.',
    'alpha_dash' => ':attribute mag alleen letters, nummers, underscores (_) en streepjes (-) bevatten.',
    'alpha_num' => ':attribute mag alleen letters en nummers bevatten.',
    'array' => ':attribute moet geselecteerde elementen bevatten.',
    'ascii' => 'De :attribute mag alleen alfanumerieke tekens en symbolen van één byte bevatten.',
    'before' => ':attribute moet een datum vóór :date zijn.',
    'before_or_equal' => ':attribute moet een datum vóór of gelijk aan :date zijn.',
    'between' => [
        'array' => ':attribute moet tussen :min en :max waardes bevatten.',
        'file' => ':attribute moet tussen :min en :max kilobytes zijn.',
        'numeric' => ':attribute moet tussen :min en :max zijn.',
        'string' => ':attribute moet tussen :min en :max karakters zijn.',
    ],
    'boolean' => ':attribute moet ja of nee zijn.',
    'can' => ':attribute bevat een waarde waar je niet bevoegd voor bent.',
    'confirmed' => 'Bevestiging van :attribute komt niet overeen.',
    'contains' => 'Het :attribute veld mist een verplichte waarde.',
    'current_password' => 'Huidig wachtwoord is onjuist.',
    'date' => ':attribute moet een datum bevatten.',
    'date_equals' => ':attribute moet een datum gelijk aan :date zijn.',
    'date_format' => ':attribute voldoet niet aan het formaat :format.',
    'decimal' => 'De :attribute moet :decimal decimalen hebben.',
    'declined' => ':attribute moet afgewezen worden.',
    'declined_if' => ':attribute moet afgewezen worden wanneer :other gelijk is aan :value.',
    'dependent_fqdn' => [
        'required_data' => 'De vereiste gegevens ontbreken.',
        'invalid_redirect_uris_format' => 'Het formaat van de opgegeven redirect URIs is ongeldig.',
        'invalid_fqdn' => 'Het FQDN-formaat is ongeldig.',
        'invalid_redirect_uri' => 'De URI :uri is ongeldig.',
        'host_mismatch' => 'De host van de URI :uri komt niet overeen met de host van het FQDN :fqdnHost.',
    ],
    'different' => ':attribute en :other moeten verschillend zijn.',
    'digits' => ':attribute moet bestaan uit :digits cijfers.',
    'digits_between' => ':attribute moet bestaan uit minimaal :min en maximaal :max cijfers.',
    'dimensions' => ':attribute heeft geen geldige afmetingen.',
    'distinct' => ':attribute heeft een dubbele waarde.',
    'doesnt_end_with' => ':attribute mag niet eindigen met één van de volgende waarden: :values.',
    'doesnt_start_with' => ':attribute mag niet beginnen met één van de volgende waarden: :values.',
    'email' => ':attribute is geen geldig e-mailadres.',
    'ends_with' => ':attribute moet met één van de volgende waarden eindigen: :values.',
    'enum' => 'Gekozen :attribute is ongeldig.',
    'exists' => ':attribute bestaat niet.',
    'extensions' => ':attribute moet een van de volgende bestandsextensies hebben: :values.',
    'file' => ':attribute moet een bestand zijn.',
    'filled' => ':attribute is verplicht.',
    'gt' => [
        'array' => ':attribute moet meer dan :value waardes bevatten.',
        'file' => ':attribute moet groter zijn dan :value kilobytes.',
        'numeric' => ':attribute moet groter zijn dan :value.',
        'string' => ':attribute moet meer dan :value tekens bevatten.',
    ],
    'gte' => [
        'array' => ':attribute moet :value of meer waardes bevatten.',
        'file' => ':attribute moet groter of gelijk zijn aan :value kilobytes.',
        'numeric' => ':attribute moet groter of gelijk zijn aan :value.',
        'string' => ':attribute moet minimaal :value tekens bevatten.',
    ],
    'hex_color' => ':attribute moet een geldige hexadecimale kleurcode zijn.',
    'image' => ':attribute moet een afbeelding zijn.',
    'in' => ':attribute is ongeldig.',
    'in_array' => ':attribute bestaat niet in :other.',
    'integer' => ':attribute moet een getal zijn.',
    'ip' => ':attribute moet een geldig IP-adres zijn.',
    'ipv4' => ':attribute moet een geldig IPv4-adres zijn.',
    'ipv6' => ':attribute moet een geldig IPv6-adres zijn.',
    'json' => ':attribute moet een geldige JSON-string zijn.',
    'list' => ':attribute moet een lijst bevatten.',
    'lowercase' => ':attribute mag alleen kleine letters bevatten.',
    'lt' => [
        'array' => ':attribute moet minder dan :value waardes bevatten.',
        'file' => ':attribute moet kleiner zijn dan :value kilobytes.',
        'numeric' => ':attribute moet kleiner zijn dan :value.',
        'string' => ':attribute moet minder dan :value tekens bevatten.',
    ],
    'lte' => [
        'array' => ':attribute moet :value of minder waardes bevatten.',
        'file' => ':attribute moet kleiner of gelijk zijn aan :value kilobytes.',
        'numeric' => ':attribute moet kleiner of gelijk zijn aan :value.',
        'string' => ':attribute moet maximaal :value tekens bevatten.',
    ],
    'mac_address' => ':attribute moet een geldig MAC-adres zijn.',
    'max' => [
        'array' => ':attribute mag niet meer dan :max waardes bevatten.',
        'file' => ':attribute mag niet meer dan :max kilobytes zijn.',
        'numeric' => ':attribute mag niet hoger dan :max zijn.',
        'string' => ':attribute mag niet uit meer dan :max tekens bestaan.',
    ],
    'max_digits' => ':attribute mag niet uit meer dan :max cijfers bestaan.',
    'mimes' => ':attribute moet een bestand zijn van het bestandstype :values.',
    'mimetypes' => ':attribute moet een bestand zijn van het bestandstype :values.',
    'min' => [
        'array' => ':attribute moet minimaal :min waardes bevatten.',
        'file' => ':attribute moet minimaal :min kilobytes zijn.',
        'numeric' => ':attribute moet minimaal :min zijn.',
        'string' => ':attribute moet minimaal :min tekens zijn.',
    ],
    'min_digits' => ':attribute moet minimaal uit :min cijfers bestaan.',
    'missing' => 'Het veld :attribute moet ontbreken.',
    'missing_if' => 'Het veld :attribute moet ontbreken als :other :value is.',
    'missing_unless' => 'Het veld :attribute moet ontbreken, tenzij :other :value is.',
    'missing_with' => 'Het veld :attribute moet ontbreken wanneer :values aanwezig is.',
    'missing_with_all' => 'Het veld :attribute moet ontbreken wanneer er :values aanwezig zijn.',
    'multiple_of' => ':attribute moet een veelvoud van :value zijn.',
    'not_in' => ':attribute is ongeldig.',
    'not_regex' => 'Het formaat van :attribute is ongeldig.',
    'numeric' => ':attribute moet een getal zijn.',
    'password' => [
        'letters' => ':attribute moet minimaal één letter bevatten.',
        'mixed' => ':attribute moet minimaal één kleine letter en één hoofdletter bevatten.',
        'numbers' => ':attribute moet minimaal één cijfer bevatten.',
        'symbols' => ':attribute moet minimaal één vreemd teken bevatten.',
        'uncompromised' => 'Het opgegeven :attribute komt voor in een datalek. Kies een ander :attribute.',
    ],
    'present' => ':attribute moet aanwezig zijn.',
    'present_if' => ':attribute moet aanwezig zijn als :other :value is.',
    'present_unless' => ':attribute moet aanwezig zijn tenzij :other :value is.',
    'present_with' => ':attribute moet aanwezig zijn als :values aanwezig is.',
    'present_with_all' => ':attribute moet aanwezig zijn als :values aanwezig zijn.',
    'prohibited' => ':attribute is niet toegestaan.',
    'prohibited_if' => ':attribute is niet toegestaan indien :other gelijk is aan :value.',
    'prohibited_unless' => ':attribute is niet toegestaan tenzij :other gelijk is aan :values.',
    'prohibits' => ':attribute is niet toegestaan in combinatie met :other.',
    'regex' => 'Het formaat van :attribute is ongeldig.',
    'required' => ':attribute is verplicht.',
    'required_array_keys' => ':attribute moet waardes bevatten voor :values.',
    'required_if' => ':attribute is verplicht indien :other gelijk is aan :value.',
    'required_if_accepted' => ':attribute is verplicht indien :other is geaccepteerd.',
    'required_if_declined' => ':attribute is verplicht indien :other is geweigerd.',
    'required_unless' => ':attribute is verplicht tenzij :other gelijk is aan :values.',
    'required_with' => ':attribute is verplicht in combinatie met :values.',
    'required_with_all' => ':attribute is verplicht in combinatie met :values.',
    'required_without' => ':attribute is verplicht als :values niet ingevuld is.',
    'required_without_all' => ':attribute is verplicht als :values niet ingevuld zijn.',
    'same' => ':attribute en :other moeten overeenkomen.',
    'size' => [
        'array' => ':attribute moet :size waardes bevatten.',
        'file' => ':attribute moet :size kilobytes groot zijn.',
        'numeric' => ':attribute moet :size zijn.',
        'string' => ':attribute moet :size tekens zijn.',
    ],
    'starts_with' => ':attribute moet beginnen met een van de volgende: :values.',
    'string' => ':attribute moet een tekst zijn.',
    'timezone' => ':attribute moet een geldige tijdzone zijn.',
    'unique' => ':attribute is al in gebruik.',
    'uploaded' => 'Het uploaden van :attribute is mislukt.',
    'uppercase' => ':attribute mag alleen hoofdletters bevatten.',
    'url' => ':attribute moet een geldige URL zijn.',
    'ulid' => 'De :attribute moet een geldige ULID zijn.',
    'uuid' => ':attribute moet een geldige UUID zijn.',

    'custom' => [
        'email' => [
            'unique' => 'Dit adres is al in gebruik.',
        ],
        'fqdn' => [
            'required' => 'Het FQDN field is verplicht.',
            'regex' => 'Het FQDN moet een geldig Fully Qualified Domain Name zijn.',
            'unique' => 'FQDN is al in gebruik.'
        ],
        'redirect_uris.*' => [
            'string' => 'Elke redirect URI moet geldige zijn.',
            'distinct' => 'Redirect URI\'s moeten uniek zijn.',
            'required' => 'De Redirect URI mag niet leeg zijn.'
        ],
        'redirect_uris' => [
            'required' => 'Er moet minstens 1 Redirect URI zijn.'
        ],
        'client_redirect_uris.*' => [
            'string' => 'Elke redirect URI moet geldige zijn.',
            'distinct' => 'Redirect URI\'s moeten uniek zijn.',
            'required' => 'De Redirect URI mag niet leeg zijn.'
        ],
        'client_redirect_uris' => [
            'required' => 'Er moet minstens 1 Redirect URI zijn.'
        ],
        'client_fqdn' => [
            'required' => 'Het FQDN field is verplicht.',
            'regex' => 'Het FQDN moet een geldig Fully Qualified Domain Name zijn.',
            'unique' => 'FQDN is al in gebruik.'
        ],

    ],

    /*
   |--------------------------------------------------------------------------
   | Custom Validation Attributes
   |--------------------------------------------------------------------------
   |
   | The following language lines are used to swap our attribute placeholder
   | with something more reader friendly such as "E-Mail Address" instead
   | of "email". This simply helps us make our message more expressive.
   |
   */

    'attributes' => [
        'email' => 'e-mailadres',
        'password' => 'wachtwoord',
        'name' => 'naam',
        'main_contact_email' => 'e-mailadres contactpersoon',
        'main_contact_name' => 'naam contactpersoon',
        'coc_number' => 'KvK nummer',
        'notes' => 'opmerkingen',
        'organisation_id' => 'organisatie naam',
        'organisation_name' => 'Organisatie naam',
        'organisation_main_contact_name' => 'Naam contactpersoon',
        'organisation_main_contact_email' => 'Email contactpersoon',
        'organisation_coc_number' => 'KVK-nummer',
        'fqdn' => 'FQDN',
    ],
];