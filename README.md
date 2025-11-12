# Installation steps
- Enable SQLite in .env: DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
- php bin/console doctrine:database:create
- php bin/console doctrine:migrations:migrate

# Tests
## Prerequisites 
- `php bin/console doctrine:database:create --env=test`
- `php bin/console doctrine:migrations:migrate --env=test -n`
- `php bin/console doctrine:fixtures:load --env=test -n`
- `php bin/console lexik:jwt:generate-keypair`

## Run tests
- `php bin/phpunit`
- `php bin/phpunit --testdox --filter=BookIndexActionTest` 