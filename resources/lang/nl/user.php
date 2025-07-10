<?php

declare(strict_types=1);

// @phpcs:disable Generic.Files.LineLength

return [
    'model_singular' => 'Gebruiker',
    'model_plural' => 'Gebruikers',

    'create' => 'Gebruiker toevoegen',
    'create_description' => 'Een e-mail met instructies voor het instellen van de inloggegevens wordt naar de nieuwe gebruiker verzonden.',
    'email' => 'E-mailadres',
    'name' => 'Naam',
    'password' => 'Wachtwoord',
    'password_confirm' => 'Wachtwoord bevestigen',
    'role' => 'Rol(len)',
    'roles' => 'Rol',
    'select_role' => 'Selecteer een rol',
    'one_time_password' => [
        'title' => 'Twee factor authenticatie',
        'code' => 'Twee factor authenticatie code',
    ],
    'actions' => 'Acties',
    'active' => 'Actief',
    'deactivate' => 'Deactiveer',
    'activate' => 'Activeer',

    'register' => [
        'title' => 'Registratie bevestigen',
        'text' => 'Om uw registratie te voltooien voor :email, stel een wachtwoord in en registreer de 2 factor authenticatie in uw authenticator app.',
        'button' => 'Registreren',
    ],

    'edit_title' => 'Gebruiker :name bewerken',

    'flash' => [
        'created' => 'Gebruiker is aangemaakt. Instructies voor het instellen van inloggegevens zijn per e-mail verstuurd.',
        'created_error' => 'Gebruiker kan niet aangemaakt worden.',
        'register_error' => 'Registratie kan niet worden afgerond.',
        'updated' => 'Gebruiker is bijgewerkt',
        'update_error' => 'De gebruiker kan niet worden bijgewerkt.',
        'reset_message' => 'Er is een email verstuurd met instructies om het wachtwoord te herstellen',
        'password_updated' => 'Wachtwoord is bijgewerkt.',
        'password_update_error' => 'Wachtwoord kan niet worden bijgewerkt.',
        'deleted' => 'Gebruiker is verwijderd',
    ],

    'reset' => [
        'title' => 'Gebruiker opnieuw instellen',
        'text' => 'Weet je zeker dat de account van gebruiker ":name" opnieuw wilt instellen? Er wordt een e-mail met instructies voor het opnieuw instellen van de inloggegevens naar de nieuwe gebruiker verzonden.',
        'message' => 'Wachtwoord reset e-mail is verstuurd',
        'button_text' => 'Wachtwoord opnieuw instellen',
    ],

    'mail' => [
        'registration' => [
            'subject' => 'Registratie voltooien',
            'text' => 'Een beheerder van :appName heeft een account voor je aangemaakt. Klik op onderstaande knop om je registratie te voltooien',
            'button_text' => 'Registratie afronden',
        ],
        'email_verification' => [
            'subject' => 'Verifieer email-adres',
            'text' => 'Klik op onderstaande knop om dit email-adres te verifiëren',
            'button_text' => 'Verifieer nu',
        ],
        'account_reset' => [
            'subject' => 'Herstel account',
            'text' => 'Een beheerder van :appName heeft je account opnieuw ingesteld. Klik op onderstaande knop om je wachtwoord opnieuw in te stellen',
            'button_text' => 'Herstel account',
        ],
        'password_reset' => [
            'subject' => 'Herstel wachtwoord',
            'text' => 'Je hebt aangegeven dat je je wachtwoord vergeten bent. Klik op onderstaande knop om je wachtwoord en 2FA opnieuw in te stellen',
            'link_expiration_text' => 'Deze link is :count minuten geldig.',
            'disclaimer' => 'Als je geen wachtwoord reset hebt aangevraagd, hoef je verder geen actie te ondernemen.',
            'button_text' => 'Herstel wachtwoord',
        ],
    ],

    'delete' => [
        'title' => 'Gebruiker verwijderen',
        'text' => 'Weet je zeker dat je gebruiker ":name" wilt verwijderen?',
    ],
];
