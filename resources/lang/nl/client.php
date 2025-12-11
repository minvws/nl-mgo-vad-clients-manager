<?php

declare(strict_types=1);

return [
    'model_plural' => "Clients",
    'organisation' => "Organisatie",
    'organisation_id' => "Organisatie",
    'id' => "Client ID",
    'owner_organisation' => [
        'name' => "Organisatie",
        'main_contact_email' => "Hoofdcontact e-mail",
    ],
    'token_endpoint_auth_method' => "Token Endpoint Auth Methode",
    'token_endpoint_auth_method_none' => "Geen",
    'token_endpoint_auth_method_client_secret_post' => "Client Secret Post",
    'redirect_uris' => "Redirect URIs",
    'redirect_uris_help' => "Voeg hier de redirect URIs toe die je wilt gebruiken voor deze client.",
    'active' => "Actief",
    'created_at' => "Aangemaakt op",
    'updated_at' => "Gewijzigd op",
    'created_at_header' => "Aangemaakt",
    'updated_at_header' => "Gewijzigd",
    'create' => "Client aanmaken",
    'created_successfully' => "Client aangemaakt",
    'edit' => "Client wijzigen",
    'updated_successfully' => "Client gewijzigd",
    'actions' => 'Acties',
    'search_placeholder' => "Zoek op ID, Organisatienaam, Hoofdcontact e-mail.",
    'search_organisation' => "Zoek organisatie...",
    'active_filter' => [
        'all' => 'Alle',
        'active' => 'Actief',
        'inactive' => 'Inactief',
    ],
    'generated_mail' => [
        'subject' => 'VAD Client Credentials Gegenereerd',
        'greeting' => 'Hallo!',
        'generated_message' => 'Uw VAD Client credentials zijn gegenereerd voor de volgende applicatie:',
        'store_secretly' => 'Draag er zorg voor dat deze clients veilig en geheim worden bewaard',
        'usage' => 'U kunt deze credentials gebruiken om met uw applicatie te authenticeren bij de VAD'
    ]
];
