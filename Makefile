.PHONY:test-feature test-unit test db-reset db-migrate db-rollback db-refresh up build composer-install env-setup generate-app-key cs-fix cs-fix-changed cs-check cs-check-changed phpstan-check phpstan-check-changed composer-check

check: composer-check cs-check phpstan-check test
check-changed: composer-check cs-check-changed phpstan-check-changed test

up: ## Create and start the services
	docker compose up --detach

build: ## Build or rebuild the services
	docker compose build --pull --no-cache

composer-check: ## Check the platform requirements
	docker compose exec php sh -lc 'composer validate && composer check'

composer-install: ## Install the dependencies
	docker compose exec php sh -lc 'composer install'

db-migrate: ##start the database migration
	docker compose exec php sh -lc 'php artisan migrate'

env-setup:
	cp .env.example .env

generate-app-key:
	docker compose exec php sh -lc 'php artisan key:generate'

cs-fix: ## Fix the code with CS Fixer
	docker compose exec php sh -lc './vendor/bin/php-cs-fixer fix --config=./.php-cs-fixer.dist.php'

cs-fix-changed: ## Fix the code in changed files with CS Fixer
	docker-compose exec php sh -lc './vendor/bin/php-cs-fixer fix --config=./.php-cs-fixer.dist.php $(shell git diff --diff-filter=ACMRTUXB HEAD --name-only -- '*.php')'

cs-check: ## Check the code with CS Fixer
	docker compose exec php sh -lc './vendor/bin/php-cs-fixer fix --config=./.php-cs-fixer.dist.php --dry-run --diff'

cs-check-changed: ## Check the code with CS Fixer
	docker compose exec php sh -lc './vendor/bin/php-cs-fixer fix --config=./.php-cs-fixer.dist.php --dry-run --diff $(shell git diff --diff-filter=ACMRTUXB HEAD --name-only -- '*.php')'

phpstan-check: ## Check the code with PHPStan
	docker compose exec php sh -lc './vendor/bin/phpstan analyse --configuration ./phpstan.neon --memory-limit 1G'

phpstan-check-changed: ## Check the code with PHPStan
	docker compose exec php sh -lc './vendor/bin/phpstan analyse --configuration ./phpstan.neon --memory-limit 1G $(shell git diff --diff-filter=ACMRTUXB HEAD --name-only -- '*.php')'

test: ## Run the application tests
	docker compose exec php sh -lc './vendor/bin/phpunit --configuration ./phpunit.xml'

test-unit: ## Run the application unit tests
	docker compose exec php sh -lc './vendor/bin/phpunit --configuration ./phpunit.xml --testsuite=Unit'

test-functional: ## Run the application feature tests
	docker compose exec php sh -lc './vendor/bin/phpunit --configuration ./phpunit.xml --testsuite=Feature'
