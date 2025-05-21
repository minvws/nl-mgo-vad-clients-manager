<?php

declare(strict_types=1);

// @phpcs:disable Generic.Files.LineLength

return [
    'login_failed' => 'Inloggen mislukt',
    'login' => 'Inloggen',
    'logout' => 'Uitloggen',
    'ratelimited' => 'Teveel inlogpogingen, probeer het opnieuw over :seconds seconden',
    'data' => 'Inloggegevens',
    'inactive' => 'Je account is inactief. Neem bij vragen contact op met de beheerder.',

    'verify_email' => [
        'title' => 'Verifieer je e-mailadres',
        'verification_sent' => 'Er is een verificatie-mail verzonden',
        'resend' => 'Opnieuw verzenden',
    ],

    'forgot_password' => [
        'title' => 'Wachtwoord vergeten?',
        'description' => 'Geen probleem! Vul je e-mailadres in en we sturen, als je e-mailadres bekend is in ons systeem, je een link om je wachtwoord te resetten.',
        'mail_sent' => 'Indien je e-mailadres bekend is in ons systeem, ontvang je een e-mail met instructies om je wachtwoord te resetten.',
        'reset_success' => 'Wachtwoord succesvol ingesteld',
        'reset_error' => 'Er ging iets mis met het opnieuw instellen van het wachtwoord',
    ],

    'one_time_password' => [
        'title' => 'Tweede factor authenticatie',
        'code' => 'Tweede factor authenticatie code',
        'description' => 'Bevestig toegang tot je account door de authenticatiecode in te voeren die is verstrekt door je authenticatie-applicatie.',
    ],
];
