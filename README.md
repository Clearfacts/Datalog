# Datalog

## Context

Tools for sending logs to DataDog

## Local development

We have some commands you can use, defined in a [Makefile](./Makefile). You can look there for anything you might need. All these commands set-up and use Docker containers. 
For more information see [cf-docs](https://github.com/Clearfacts/cf-docs/blob/66552172fedf8663a0d8a7d165d076565035218f/dev/LocalDevSetup.md).

### Installation

- Make sure you have composer installed globally and have php 7.4 or higher
- Clone the project from github
- `cd <folder-name>`
- `make init`
- register the processor 

```
Datalog\Processor\SessionRequestProcessor:
    arguments:
        - '@session'
    tags:
        - { name: monolog.processor, method: processRecord }
```

## Technical debt links

[Barometer IT](https://wolterskluwer.barometerit.com/b/system/041800002496)
[SonarQube Project](https://sonarqube.cloud-dev.wolterskluwer.eu//dashboard?id=clearfacts%3ADatalog)