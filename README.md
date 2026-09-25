# Datalog

## Context

PHP library of tools for formatting logs for Datadog and propagating correlation IDs. The package supplies components to consuming applications; it does not run a standalone service.

Library code is in `src/` (correlation propagation, formatters, handlers, processors, and tools); PHPUnit tests are in `tests/`. Dependency and command definitions are in `composer.json` and `Makefile`.

## Local development

We have some commands you can use, defined in a [Makefile](./Makefile). You can look there for anything you might need. The setup, dependency, test, and check targets use a private Docker image (and pass `COMPOSER_AUTH` into the container); `make list` only prints the targets.
For more information see [cf-docs](https://github.com/Clearfacts/cf-docs/blob/66552172fedf8663a0d8a7d165d076565035218f/dev/LocalDevSetup.md).

### Installation

- PHP 8 or higher with the JSON extension, Composer, and the dependencies in [composer.json](./composer.json). For Makefile targets, Docker and access to the private ECR image are also required.
- Clone the project from github
- `cd <folder-name>`
- `make init` (installs dependencies and runs the Composer setup script, which copies a PHP CS config and sets up Git hooks; this may require credentials and writes local files)
- register the processor 

```
Datalog\Processor\SessionRequestProcessor:
    arguments:
        - '@session'
    tags:
        - { name: monolog.processor, method: processRecord }
```

### Local checks

With dependencies already installed, run `vendor/bin/phpunit tests/Formatter/KeyValueFormatterTest.php` for one test file using local PHP, or `make phpunit` for the full suite in the project's Docker image. See the [run-tests skill](./.agents/skills/run-tests/SKILL.md) for scoped container runs and reporting. The `make test` target instead updates dependencies twice (lowest and highest versions) before running PHPUnit; it is not a read-only local test command.

`make phpcs` runs PHP CS Fixer in dry-run mode, but requires the locally generated `.php-cs-fixer.dist.php` from setup and the private Docker image. `make phpcs-fix` modifies files. `make psalm` is defined, but its executable is not declared in this package's dev dependencies, so availability depends on the installed toolchain. This repository has no build target or configured coverage command.

Repository-specific agent guidance is in [AGENTS.md](./AGENTS.md); local test procedure is in the [run-tests skill](./.agents/skills/run-tests/SKILL.md).

## Technical debt links

[Barometer IT](https://wolterskluwer.barometerit.com/b/system/041800002496)
[SonarQube Project](https://sonarqube.cloud-dev.wolterskluwer.eu/dashboard?id=clearfacts%3ADatalog)
[Black Duck Project](https://wolterskluwer.app.blackduck.com/api/projects/750ee2f8-80a5-4d6b-9922-206979db3201)
[Checkmarx Project](https://test4tools.cchaxcess.com/CxWebClient/ProjectStateSummary.aspx?projectid=17827)

Next line is for blackduck to ignore this repository, as it is a vendor package and thus does not have a composer.lock file with actual dependencies
- [x] zero dependencies
