[![PHP](https://img.shields.io/badge/PHP-7.0%2B-blue.svg)](https://php.net/migration70)
[![Build Status](https://travis-ci.org/etraxis/etraxis.svg?branch=master)](https://travis-ci.org/etraxis/etraxis)
[![Code Coverage](https://scrutinizer-ci.com/g/etraxis/etraxis/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/etraxis/etraxis/?branch=master)
[![Code Quality](https://scrutinizer-ci.com/g/etraxis/etraxis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/etraxis/etraxis/?branch=master)

eTraxis is an issue tracking system with ability to set up an unlimited number of customizable workflows.
It can be used to track almost anything, though the most popular cases are a *bug tracker* and a *helpdesk system*.

### Features

* Custom workflows
* Fine-tuned permissions
* History of events and changes
* Filters and views
* Attachments
* Project metrics
* Authentication through Bitbucket, GitHub or Google
* Authentication through Active Directory (LDAP)
* MySQL and PostgreSQL support
* Multilingual support and localization
* Mobile-friendly web interface
* Customizable UI
* OS and browser independence
and more...

### Prerequisites

* [PHP](https://php.net/)
* [Composer](https://getcomposer.org/)

### Install

```bash
composer install
./bin/console doctrine:database:create
./bin/console doctrine:schema:create
```

### Development

```bash
./bin/console --env=test --no-interaction doctrine:database:create
./bin/console --env=test --no-interaction doctrine:schema:create
./vendor/bin/php-cs-fixer fix
./vendor/bin/simple-phpunit --coverage-html=var/coverage
```
