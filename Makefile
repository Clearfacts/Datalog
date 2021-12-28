# datalog makefile

.DEFAULT_GOAL := list

.PHONY: list
list:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: ## Setup this project.
	@make composer

# Composer commands
composer: ## Do a composer install for the php project.
	@composer install

# Linting and testing	
args?=
test: ## Run all tests with an optional parameter `args` to run a specific suite or test-file, or pass some other testing arguments.
	@vendor/bin/phpunit $(args)
