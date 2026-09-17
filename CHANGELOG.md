# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [Unreleased]

### Added

- Support for Symfony 8.1

### Changed

- **[BC-BREAK]** The bundle's own service definitions moved from XML to the PHP config
  format. Symfony 8 removed the XML configuration format entirely.
- **[BC-BREAK]** Minimum PHP version is now 8.4.
- Renamed to `pixelfederation/event-store-symfony-bundle`; `replace`s the upstream
  `prooph/event-store-symfony-bundle`, so it works as a drop-in replacement.

### Removed

- **[BC-BREAK]** Support for every Symfony version other than 7.4 (LTS) and 8.1. Symfony 5.4
  was the only one still needing `HandlerFailedException::getNestedExceptions()`.
- `ProophEventStoreExtension::getNamespace()`. Symfony 8 removed
  `ExtensionInterface::getNamespace()` along with the XML configuration format, so bundle
  configuration can no longer be written in XML on Symfony 8.

## [0.5.0] - 2018-05-03

### Added

- Support for Symfony 6
- Support for PHP 8.1

### Deprecated

- Support for Symfony 3.x and 4.x (Use version v9.x)

## [0.5.0] - 2018-05-03

### Added

 - Allow services referenced in config to be prefixed with @ (#41)
 - Add FQCN alias for `Prooph\Common\messaging\MessageFactory` (#45, thanks to @gquemener)

### Deprecated

 - Deprecate using a FQCN instead of a service id for projections.
   Support for this kind of configuration will be removed in v1.0. (#43) 

### Changed

 - Enhance validation of repository configuration (#44, thanks to @gquemener) 

### Removed

 - **[BC-BREAK]** Remove automatically created aliases for projections and projection-managers (#43)

### Fixed

 - Fix projection configuration via tags (#42, #43)


## [0.4.0] - 2018-02-21

### Changed

 - Support Symfony 4, drop Symfony <= 3.3 (#33, #38, thanks to @kejwmen, @mkurzeja) 
