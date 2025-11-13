# 📚 PHP/Symfony Boilerplate
Native Symfony v7 and php v8.4 API enterprise project setup
- ✅ Symfony 7 with PHP 8.4
- ✅ Stateless authentication with JWT (`lexik/jwt-authentication-bundle`)
- ✅ Token refresh via `gesdinet/jwt-refresh-token-bundle`
- ✅ Request validation via DTOs
- ✅ Consistent output using **Neomerx JSON:API Encoder**
- ✅ Automatic schema-based JSON:API responses
- ✅ Clean controller architecture using `Output` response wrapper
- ✅ Doctrine ORM with UUID identifiers
- ✅ Fixtures for demo users (admin and regular)

---

## 🧩 Project Structure

src/  
├── Controller/Api/ # Route-based API controllers  
├── DTO/ # Input DTOs for request validation  
├── Entity/ # Doctrine entities  
├── Resource/ # Read-only data transfer objects for API responses  
├── Schema/ # JSON:API schemas for Neomerx encoder  
├── Service/ # Business logic and entity services  
├── EventListener/ # Global listeners (e.g. JsonApiViewListener)  
└── Resolver/ # Param converters

---

## ⚙️ Requirements

- PHP 8.4
- Composer
- Symfony CLI
- SQLite/MySQL/PostgreSQL

---

## 🧰 Installation
- `git clone https://github.com/mholod/BookTrackerApi.git`
- `cd BookTrackerApi`
- `composer install`
- Optionally enable SQLite in .env: `DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db`
- `php bin/console doctrine:database:create`
- `php bin/console doctrine:migrations:migrate`
- `php bin/console lexik:jwt:generate-keypair`

---

## 🧪 Tests
- `php bin/console doctrine:database:create --env=test`
- `php bin/console doctrine:migrations:migrate --env=test -n`
- `php bin/console doctrine:fixtures:load --env=test -n`
- `php bin/phpunit`
- `php bin/phpunit --testdox --filter=BookIndexActionTest` 

---

## Phpstan
- `php ./vendor/bin/phpstan analyse --memory-limit=2G`

---

## TODO
- Add OpenAPI documentation
- Add entity processor 
- Add json error handling
