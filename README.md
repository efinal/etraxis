[![PHP](https://img.shields.io/badge/PHP-7.0%2B-blue.svg)](https://php.net/migration70)

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
```

### Development

```bash
./vendor/bin/php-cs-fixer fix
./vendor/bin/simple-phpunit --coverage-html=var/coverage
```
