# Hosting changelog

Dit bestand bevat wijzigingen waarmee rekening moet worden gehouden bij het uitrollen van een release op de hostingomgeving. Dit kunnen nieuwe omgevingsvariabelen zijn, actieve scripts enz.

## Initiele stappen

Bij een installatie op een nieuwe omgeving zullen de volgende stappen altijd uitgevoerd moeten worden:

- verwerk migraties in `database/sql` om de database-tabellen te maken
- zet de environment variabelen conform de eisen van de omgeving: zie de [voorbeeld `.env` file](./.env.example)
- maak een unieke applicatie key aan: `php artisan key:generate`
- maak een admin-user met de naam en het email adres van de applicatiebeheerder (op non-productie omgevingen: de Scrum Master van het project): `php artisan user:create-admin`

## Changelog per Tag

### Next release

...

### [0.6.0]

    - No additional actions needed

### [0.5.0]

Added:

- A queue worker that runs the `artisan queue:work` command to process jobs in the background.
- The default `QUEUE_CONNECTION` must be set to `database`.

### [0.4.0]

Added:

- VAD_NOTIFIABLES
  - A CSV string of webhook URL's that should be notified when clients are updated

### [0.3.0]

    - No additional actions needed

### [0.2.0]

    - Bug fixes

### [0.1.1]

    - First release 
