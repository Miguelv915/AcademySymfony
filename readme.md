# PLANTILLA DEMO

Manageable web system based on Symfony 7 and metronic theme (cdn).

## Installation

Use composer [composer](https://getcomposer.org/) to install pageadmin.

```bash
composer install
```

## DB Schema

```bash
php bin/console doctrine:schema:update --dump-sql --force
```

## Fixtures

```bash
php bin/console doctrine:fixtures:load
```

## Environment

create file .env.local in root project.
```bash
## GLOBAL ##
APP_ENV=dev
APP_SECRET=7e9285e41c54dcd7e80b10af8881dca97987
MESSENGER_TRANSPORT_DSN=doctrine://default

## UPDATE ##
DATABASE_URL="mysql://root@127.0.0.1:3306/plantilla_demo?serverVersion=8.2.0&charset=utf8mb4"
TELEGRAM_DSN=telegram://TOKEN@default?channel=CHAT_ID
MAILER_DSN=smtp://localhost
```

## Webpack Encore Symfony (optional)

Use UX Live Component [Docs](https://github.com/symfony/ux-live-component).

```bash
yarn install --force
yarn watch
```

## Theme
Use theme admin [metronic 8](https://preview.keenthemes.com/metronic8/demo8/index.html).


#Tools
## Php-cs-fixer
Optimice and formatting code
```bash
.\vendor\bin\php-cs-fixer fix src/
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
v2.0 [PIDIA SRL](http://www.pidia.pe/)