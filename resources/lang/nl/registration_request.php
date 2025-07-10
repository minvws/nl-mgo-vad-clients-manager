<?php

return [
    'title' => 'VAD Client aanvragen',
    'description' => [
        'purpose' => 'Via dit formulier kunt u een nieuwe VAD Client aanvragen. Na het indienen van uw aanvraag wordt deze beoordeeld door de beheerder. U ontvangt een e-mail zodra uw aanvraag is goedgekeurd of afgewezen.',
        'process' => 'Na goedkeuring van uw aanvraag wordt een nieuwe VAD Client aangemaakt en ontvangt u de benodigde gegevens om de client te kunnen gebruiken.',
    ],
    'organisation_name' => 'Naam organisatie',
    'organisation_main_contact_name' => 'Naam contactpersoon',
    'organisation_main_contact_email' => 'E-mailadres contactpersoon',
    'organisation_coc_number' => 'KvK-nummer',
    'client_fqdn' => 'FQDN',
    'client_fqdn_help' => 'Het domein waarop uw client actief is.',
    'client_redirect_uris' => 'Redirect URIs',
    'client_redirect_uris_help' => 'Voeg hier de redirect URIs toe die je wilt gebruiken voor deze client.',
    'submit' => 'Indienen',
    'flash' => [
        'created' => 'Uw VAD Client aanvraag is succesvol ingediend.',
    ],
    'thank_you' => [
        'title' => 'Bedankt voor uw aanvraag',
        'subtitle' => 'Uw aanvraag is succesvol ontvangen',
        'request_details' => 'Details van uw aanvraag',
        'next_steps' => [
            'title' => 'Vervolgproces',
            'step1' => 'Uw aanvraag wordt beoordeeld door de beheerder',
            'step2' => 'U ontvangt een e-mail zodra uw aanvraag is goedgekeurd of afgewezen',
            'step3' => 'Na goedkeuring wordt uw VAD Client aangemaakt',
            'step4' => 'U ontvangt de benodigde gegevens om de client te kunnen gebruiken',
        ],
        'contact' => 'Heeft u vragen? Neem dan contact op met de beheerder.',
    ],
]; 