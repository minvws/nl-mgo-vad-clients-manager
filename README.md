# VAD Client Manager

## Disclaimer

This project and all associated code serve solely as documentation
and demonstration purposes to illustrate potential system
communication patterns and architectures.

This codebase:

- Is NOT intended for production use
- Does NOT represent a final specification
- Should NOT be considered feature-complete or secure
- May contain errors, omissions, or oversimplified implementations
- Has NOT been tested or hardened for real-world scenarios

The code examples are only meant to help understand concepts and demonstrate possibilities.

By using or referencing this code, you acknowledge that you do so at your own
risk and that the authors assume no liability for any consequences of its use.

## Setup and install instructions

### Requirements

- [Docker](https://docs.docker.com/get-started/get-docker/) and [Docker Compose](https://docs.docker.com/compose/).
- [Task](https://taskfile.dev/#/installation).
- [Composer](https://getcomposer.org/download/).
  - depending on your OS you need to install `php` as well; `brew install composer` installs php & composer on MacOS.
  - run `composer config -g` to initialise the global configuration

### Installation

This repository uses private packages hosted on the minvws GitHub account. To access these packages, you need to authenticate
using a GitHub Personal Access Token (PAT). This token should contain the scopes repo and read:packages and should be added
to your local `auth.json` file. This file is typically located in your composer directory (e.g. ~/.config/composer/auth.json
or ~/.composer/auth.json), but you can also just create an `auth.json` file in the project root. Just be sure to not commit that :) 

Add the following content to the auth.json file, replacing your_github_token_here with the token you generated:

```json
{
    "github-oauth": {
        "github.com": "your_github_token_here"
    }
}
```

Clone this repository and run in the root of your project:

  ```bash
  task init
  ```
  
This will do the following:
- Copy the .env.example to .env
- Install the composer dependencies
- Start the docker containers
- Generate a new application key
- Run the migrations and seed the database
- Install the frontend dependencies
- Build the frontend assets
- Create an admin user
Note: In the output there will be a link present to finish the registration of the admin user before you can login

The next time, if you want to simply start the application without initialization, run the following command:

```bash
task up
```

So what the `task init` does is to prepare your environment from scratch, so it does all kind of installations, prepopulates the database,
creates an admin user and has also the container `nl-mgo-vad-clients-manager` up and running.
In case you don't have any radical updates to run and you simply want to run the application, then you 'd better use the `task up` command.

To stop the containers you can run the following command:

```bash
task stop
```

### Troubleshooting
* If you want to pre-populate the db with seeded data, you can run in your terminal the following command:

```bash
vendor/bin/sail artisan db:seed
```

* If you want to manually install the `php composer`, then
in your terminal do `./vendor/bin/sail shell`. This will allow you to interact with the Laravel Sail container, which is the
default Docker development environment. Sail provides a lightweight command-line interface for managing various aspects
of your Laravel application's Docker containers.
Inside that container you can now run `composer install`.


## Default configuration

### Cache driver
When you first install the project the default cache driver is set to `file` instead of `database`. After your first
install you can switch this in the `.env` by changing the `CACHE_STORE` variable. The `file` driver is not recommended
for production environments.

For other driver options see the [Laravel documentation](https://laravel.com/docs/11.x/cache#configuration).

### Session driver
The default session driver is set to `database` via the `SESSION_DRIVER` variable in the `.env`. This is recommended
for production environments. For other driver options see the [Laravel documentation](https://laravel.com/docs/11.x/session#configuration).


### Creating user

After you've set up the project you can create a user by running the following command:

```bash
vendor/bin/sail artisan user:create-admin
```

This will guide you to create a new admin user by asking for an email address and name. You can also provide the email
and name as parameters to the command.

```bash
vendor/bin/sail artisan user:create-admin user@email.tld Username
```

This command will return the url to a registration page, where the user can set up a password and their 2-factor-authentication.

### Emails

By default, the application sends the emails to the log file. To debug or view the emails in a nice interface you can
use the supplied MailPit container. This container will catch all outgoing emails and show them in a web interface.
To use this you need to make some changes in your `.env` file:
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```
After this change you can view the emails by browsing to [http://localhost:8025](http://localhost:8025)


## Contributing

If you encounter any issues or have suggestions for improvements, please feel free to open an issue or submit a pull
request on the GitHub repository of this package.

## License

This repository follows the [REUSE Specfication v3.2](https://reuse.software/spec-3.2/). The code is available under the
EUPL-1.2 license, but the fonts and images are not. Please see [LICENSES/](./LICENSES), [REUSE.toml](./REUSE.toml) and
the individual `*.license` files (if any) for copyright and license information.

## Information

This repo is based on icore-laravel-starter. Download the latest [release](https://github.com/minvws/icore-laravel-starter/releases/latest)

