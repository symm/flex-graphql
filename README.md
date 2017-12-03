# Symfony Flex and GraphQL example

[![Travis](https://img.shields.io/travis/symm/flex-graphql.svg)]()

This repo is a sandbox for playing with [GraphQL](http://graphql.org/learn/) in PHP.

## Overview

Stack consists of:

- [Symfony Flex](https://symfony.com/doc/current/setup/flex.html) as the base framework
- [WebOnyx GraphQL](https://github.com/webonyx/graphql-php) library for GraphQL support
- [Overblog DataLoader](https://github.com/overblog/dataloader-php) for solving [N+1 issues](https://spin.atomicobject.com/2017/05/15/optimize-graphql-queries/)
- [GraphQL Playground](https://github.com/graphcool/graphql-playground) WebUI for tinkering
- [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html) for persistence
- [PHPUnit](https://phpunit.de/) for functional testing

## Getting started

Clone the repo

```
git clone git@github.com:symm/flex-graphql.git
```

Setup

```
composer install
composer start
```

Then visit the url provided (usually [http://127.0.0.1:8000](http://127.0.0.1:8000))

## Test Suite

Run the test suite via

```
composer test
```

## Linting

Check linting via

```
composer lint
```

Fix issues via

```
vendor/bin/php-cs-fixer fix
```
