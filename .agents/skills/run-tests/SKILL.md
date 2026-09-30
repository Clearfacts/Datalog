---
name: run-tests
description: Run local Datalog PHPUnit tests after changes to src/ or tests/, or when asked to verify library behavior.
---

# Run local tests

Use this skill to verify PHP library behavior without contacting a backend. Read [README.md](../../../README.md) for setup caveats and [AGENTS.md](../../../AGENTS.md) for repository boundaries.

## Prerequisites

From the repository root, use already-installed Composer dev dependencies (`vendor/bin/phpunit` and `vendor/autoload.php`). Direct runs require PHP 8+ locally; the Makefile runs PHPUnit in a private PHP Docker image, requiring Docker, image access, and an interactive terminal (`docker run -it`). Its container receives `COMPOSER_AUTH`. `phpunit.xml.dist` bootstraps the autoloader and discovers `tests/`; tests live in `tests/<area>/*Test.php` and map to `src/`. Some tests extend `tests/TestCase.php`, which supplies `callPrivateMethod()`; use that helper where applicable.

## Commands

- One file: `vendor/bin/phpunit tests/Formatter/KeyValueFormatterTest.php`
- Several related tests (directory): `vendor/bin/phpunit tests/Processor/`
- Full suite in the project's container: `make phpunit`
- Full suite with a working local PHP toolchain: `vendor/bin/phpunit`

There is no configured coverage command in `phpunit.xml.dist` or the Makefile. First run the most relevant file or directory, then broaden to the suite when needed. `make phpunit` runs the full suite using the Makefile's Docker image; its recipe does not forward a test path. For a scoped container run, use that same Docker invocation with a test path appended, for example:

`docker run -it --volume "$PWD:/var/www/html" -e COMPOSER_AUTH -e COMPOSER_MEMORY_LIMIT=-1 077201410930.dkr.ecr.eu-west-1.amazonaws.com/cf-docker-base-php:8.1.21-dev vendor/bin/phpunit tests/Formatter/KeyValueFormatterTest.php`

`make test` performs dependency updates before each run, so do not use it as a quick local check.

Do not install dependencies or run setup just to test without authorization: the Docker-based targets can require credentials and alter local files or hooks. Tests should use mocks rather than real backends or external services; do not run destructive cleanup or change unrelated work. Report the exact command and exit status, any failures, and any validation skipped. If dependencies or the Docker image are unavailable, report the blocker and what could not be run rather than claiming a pass.
