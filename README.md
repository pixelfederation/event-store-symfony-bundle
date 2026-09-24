# ProophEventStore Symfony bundle

> **PixelFederation fork** of [prooph/event-store-symfony-bundle](https://github.com/prooph/event-store-symfony-bundle),
> which is no longer maintained (last release: May 2024) and does not support Symfony 8.
> This fork adds Symfony 8 support. It `replace`s the upstream package, so it can be used as a drop-in replacement.
[![Tests Status](https://github.com/pixelfederation/event-store-symfony-bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/pixelfederation/event-store-symfony-bundle/actions/workflows/tests.yml)
[![Analyse Status](https://github.com/pixelfederation/event-store-symfony-bundle/actions/workflows/static-analyse.yml/badge.svg)](https://github.com/pixelfederation/event-store-symfony-bundle/actions/workflows/static-analyse.yml)
[![Coverage Status](https://coveralls.io/repos/pixelfederation/event-store-symfony-bundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/pixelfederation/event-store-symfony-bundle?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

## Requirements

- PHP 8.4 or higher
- Symfony 7.4 or 8.1

## Installation

Installation of this Symfony bundle uses Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

Run `composer require pixelfederation/event-store-symfony-bundle` to install the bundle.

> See [Symfony Proophessor-Do demo application](https://github.com/prooph/proophessor-do-symfony) for an example.

## Migration from 0.8.0 to 0.9.0

After 0.8.0 `prooph/event-sourcing` dependency was dropped. If you implemented your business logic based on that component 
you can still run your application although, you will have to do some additional work. 
Please follow [migration instructions](doc/migrating_from_0.8.0.md) for that. 

## Documentation
For the latest online documentation visit [http://getprooph.org/](http://getprooph.org/ "Latest documentation").

Documentation is [in the doc tree](doc/).

## Support

- Ask questions on Stack Overflow tagged with [#prooph](https://stackoverflow.com/questions/tagged/prooph).
- File issues at [https://github.com/pixelfederation/event-store-symfony-bundle/issues](https://github.com/pixelfederation/event-store-symfony-bundle/issues).
- Say hello in the [prooph gitter](https://gitter.im/prooph/improoph) chat.

## Contribute

Please feel free to fork and extend existing or add new plugins and send a pull request with your changes!
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.

## License

Released under the [New BSD License](LICENSE.md).
