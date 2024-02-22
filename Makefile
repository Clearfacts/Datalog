# datalog makefile

.DEFAULT_GOAL := list

docker=docker run -it --volume $$PWD:/var/www/html -e COMPOSER_AUTH -e COMPOSER_MEMORY_LIMIT=-1 077201410930.dkr.ecr.eu-west-1.amazonaws.com/cf-docker-base-php:8.1.24-dev

.PHONY: list
list:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: ## Setup this project.
	@make composer
	@make setup

bash: ## ssh into the php container.
	@$(docker) bash

# Composer commands
composer: ## Do a composer install.
	@$(docker) composer.phar install
composer-highest: ## Do a composer update with highest available versions.
	@$(docker) composer.phar update
composer-lowest: ## Do a composer update with lowest available versions.
	@$(docker) composer.phar update --prefer-lowest

test: ## Run all tests with oldest and newest possible dependencies.
	make composer-lowest phpunit
	make composer-highest phpunit

phpunit: ## Run phpunit
	@$(docker) vendor/bin/phpunit

setup: ## Setup git-hooks
	@$(docker) composer.phar run set-up

copy-phpcs-config: ## Setup phpcs config
	@$(docker) composer.phar run copy-phpcs-config

options?=
files?=src/
phpcs: ## Check phpcs.
	@$(docker) vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)

phpcs-fix: ## Check phpcs and try to automatically fix issues.
	@$(docker) vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)

psalm: ## Check phpcs and try to automatically fix issues.
	@$(docker) vendor/bin/psalm