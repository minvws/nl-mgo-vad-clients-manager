<?php

declare(strict_types=1);

return [
    'profile_of' => 'Profiel van',
    'title' => 'Profiel',

    'personal' => [
        'title' => 'Persoonlijke informatie',
        'subtitle' => 'Bijwerken van persoonlijke informatie als naam en e-mailadres',
    ],

    'flash' => [
        'updated' => 'Profiel bijgewerkt',
        'password' => 'Wachtwoord bijgewerkt',
        'otp' => 'Twee factor authenticatie bijgewerkt',
    ],

    'password' => [
        'title' => 'Wachtwoord',
        'subtitle' => 'Bijwerken van wachtwoord',
        'password_current' => 'Huidig wachtwoord',
        'password_confirm' => 'Bevestig wachtwoord',
        'password_new' => 'Nieuw wachtwoord',
    ],

    '2fa' => [
        'title' => 'Twee factor authenticatie',
        'subtitle' => 'Twee factor authenticatie zorgt voor extra beveiliging',
        'confirm' => 'Bevestigen',
        'enable' => 'Inschakelen',
        'disable' => 'Uitschakelen',
        'secret' => 'Sleutel',
        'reset' => 'Opnieuw instellen',
        'reset_success' => 'Tweefactorauthenticatie is opnieuw ingesteld',
        'invalid_code' => 'De opgegeven code is ongeldig',
        'reset_title' => '2FA opnieuw instellen',
        'reset_instructions' => 'Scan de QR-code met je authenticator app en voer de 6-cijferige code in om te bevestigen.',
        'verification_code' => 'Verificatiecode',
        'invalid' => 'De ingevoerde 2FA-code is ongeldig.',
        'non_decryptable_secret' => 'De opgegeven geheime sleutel is ongeldig. Dit hoort niet te gebeuren. Neem contact op met de beheerder.',
    ],
];
