# Rinvex Tags Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](CONTRIBUTING.md).


## [v7.1.1] - 2023-06-29
- Refactor resource loading and publishing

## [v7.1.0] - 2023-05-02
- 92c9893: Add support for Laravel v11, and drop support for Laravel v9
- fe5e1cc: Upgrade spatie/laravel-translatable to v6.5 from v6.0
- 0aa8804: Upgrade spatie/laravel-sluggable to v3.4 from v3.3
- 510cf86: Update phpunit to v10.1 from v9.5

## [v7.0.0] - 2023-01-09
- Add Relation::morphMap
- Tweak artisan commands registration
- Drop PHP v8.0 support and update composer dependencies
- Utilize PHP 8.1 attributes feature for artisan commands

## [v6.1.2] - 2022-08-30
- Update exists and unique validation rules to use models instead of tables

## [v6.1.1] - 2022-06-20
- Update composer dependencies spatie/laravel-translatable to ^6.0.0 from ^5.2.0

## [v6.1.0] - 2022-02-14
- Update composer dependencies to Laravel v9
- Add support for model HasFactory
- Check before detaching tags if deleted entity was soft deleted

## [v6.0.0] - 2021-08-22
- Drop PHP v7 support, and upgrade rinvex package dependencies to next major version
- Update composer dependencies

## [v5.0.5] - 2021-05-24
- Merge rules instead of resetting, to allow adequate model override
- Update spatie/eloquent-sortable composer package to v4.0.0
- Update spatie/laravel-translatable composer package to v5.0.0
- Update spatie/laravel-sluggable composer package to v3.0.0

## [v5.0.4] - 2021-05-11
- Fix constructor initialization order (fill attributes should come next after merging fillables & rules)

## [v5.0.3] - 2021-05-07
- Drop old MySQL versions support that doesn't support json columns
- Upgrade to GitHub-native Dependabot
- Utilize SoftDeletes

## [v5.0.2] - 2021-02-06
- Simplify service provider model registration into IoC
- Update phpdoc to match method signature
- Enable StyleCI risky mode

## [v5.0.1] - 2020-12-25
- Add support for PHP v8

## [v5.0.0] - 2020-12-22
- Upgrade to Laravel v8
- Move custom eloquent model events to module layer from core package layer
- Refactor and tweak Eloquent Events

## [v4.1.1] - 2020-07-16
- Update validation rules

## [v4.1.0] - 2020-06-15
- Update validation rules
- Drop using rinvex/laravel-cacheable from core packages for more flexibility
  - Caching should be handled on the application layer, not enforced from the core packages
- Drop PHP 7.2 & 7.3 support from travis
- Drop slugifying group attribute

## [v4.0.6] - 2020-05-30
- Remove default indent size config
- Add strip_tags validation rule to string fields
- Specify events queue
- Explicitly specify relationship attributes
- Add strip_tags validation rule
- Explicitly define relationship name

## [v4.0.5] - 2020-04-12
- Fix ServiceProvider registerCommands method compatibility

## [v4.0.4] - 2020-04-09
- Tweak artisan command registration
- Reverse commit "Convert database int fields into bigInteger"
- Refactor publish command and allow multiple resource values

## [v4.0.3] - 2020-04-04
- Fix namespace issue

## [v4.0.2] - 2020-04-04
- Enforce consistent artisan command tag namespacing
- Enforce consistent package namespace
- Drop laravel/helpers usage as it's no longer used

## [v4.0.1] - 2020-03-20
- Convert into bigInteger database fields
- Add shortcut -f (force) for artisan publish commands
- Fix migrations path

## [v4.0.0] - 2020-03-15
- Upgrade to Laravel v7.1.x & PHP v7.4.x

## [v3.0.4] - 2020-03-13
- Tweak TravisCI config
- Add migrations autoload option to the package
- Tweak service provider `publishesResources`
- Remove indirect composer dependency
- Drop using global helpers
- Update StyleCI config

## [v3.0.3] - 2019-12-18
- Fix `migrate:reset` args as it doesn't accept --step
- Create event classes and map them in the model

## [v3.0.1] - 2019-09-24
- Add missing laravel/helpers composer package

## [v3.0.0] - 2019-09-23
- Upgrade to Laravel v6 and update dependencies

## [v2.1.1] - 2019-06-03
- Enforce latest composer package versions

## [v2.1.0] - 2019-06-02
- Update composer deps
- Drop PHP 7.1 travis test
- Refactor migrations and artisan commands, and tweak service provider publishes functionality

## [v2.0.0] - 2019-03-03
- Rename environment variable QUEUE_DRIVER to QUEUE_CONNECTION
- Require PHP 7.2 & Laravel 5.8
- Apply PHPUnit 8 updates

## [v1.0.1] - 2018-12-22
- Update composer dependencies
- Add PHP 7.3 support to travis
- Fix MySQL / PostgreSQL json column compatibility

## [v1.0.0] - 2018-10-01
- Enforce Consistency
- Support Laravel 5.7+
- Rename package to rinvex/laravel-tags

## [v0.0.5] - 2018-09-21
- Update travis php versions
- Fix wrong function names
- Drop StyleCI multi-language support (paid feature now!)
- Update composer dependencies
- Prepare and tweak testing configuration
- Highlight variables in strings explicitly
- Update PHPUnit options
- Add tag model factory

## [v0.0.4] - 2018-02-18
- Add PublishCommand to artisan
- Update supplementary files
- Move slug auto generation to the custom HasSlug trait
- Add Rollback Console Command
- Add missing composer dependencies
- Add PHPUnitPrettyResultPrinter
- Remove useless scopes
- Refactor taggable trait for more simple and clean code
- Update composer dependencies
- Typehint method returns
- Drop useless model contracts (models already swappable through IoC)
- Add Laravel v5.6 support
- Simplify IoC binding
- Refactor parseTags
- Fix wrong parameter names
- Add force option to artisan commands
- Drop Laravel 5.5 support

## [v0.0.3] - 2017-09-09
- Fix many issues and apply many enhancements
- Rename package rinvex/laravel-tags from rinvex/taggable

## [v0.0.2] - 2017-06-29
- Enforce consistency
- Update validation rules
- Add Laravel 5.5 support
- Fix detaching tags method
- Fix multiple bugs & issues
- Change integer column length
- Tweak model event registration
- Rename tags type to tags group
- Enforce more secure approach using model fillable instead of guarded

## v0.0.1 - 2017-04-08
- Rename package to "rinvex/taggable" from "rinvex/tag" based on 3b6a727

[v7.1.1]: https://github.com/rinvex/laravel-tags/compare/v7.1.0...v7.1.1
[v7.1.0]: https://github.com/rinvex/laravel-tags/compare/v7.0.0...v7.1.0
[v7.0.0]: https://github.com/rinvex/laravel-tags/compare/v6.1.2...v7.0.0
[v6.1.2]: https://github.com/rinvex/laravel-tags/compare/v6.1.1...v6.1.2
[v6.1.1]: https://github.com/rinvex/laravel-tags/compare/v6.1.0...v6.1.1
[v6.1.0]: https://github.com/rinvex/laravel-tags/compare/v6.0.0...v6.1.0
[v6.0.0]: https://github.com/rinvex/laravel-tags/compare/v5.0.5...v6.0.0
[v5.0.5]: https://github.com/rinvex/laravel-tags/compare/v5.0.4...v5.0.5
[v5.0.4]: https://github.com/rinvex/laravel-tags/compare/v5.0.3...v5.0.4
[v5.0.3]: https://github.com/rinvex/laravel-tags/compare/v5.0.2...v5.0.3
[v5.0.2]: https://github.com/rinvex/laravel-tags/compare/v5.0.1...v5.0.2
[v5.0.1]: https://github.com/rinvex/laravel-tags/compare/v5.0.0...v5.0.1
[v5.0.0]: https://github.com/rinvex/laravel-tags/compare/v4.1.1...v5.0.0
[v4.1.1]: https://github.com/rinvex/laravel-tags/compare/v4.1.0...v4.1.1
[v4.1.0]: https://github.com/rinvex/laravel-tags/compare/v4.0.6...v4.1.0
[v4.0.6]: https://github.com/rinvex/laravel-tags/compare/v4.0.5...v4.0.6
[v4.0.5]: https://github.com/rinvex/laravel-tags/compare/v4.0.4...v4.0.5
[v4.0.4]: https://github.com/rinvex/laravel-tags/compare/v4.0.3...v4.0.4
[v4.0.3]: https://github.com/rinvex/laravel-tags/compare/v4.0.2...v4.0.3
[v4.0.2]: https://github.com/rinvex/laravel-tags/compare/v4.0.1...v4.0.2
[v4.0.1]: https://github.com/rinvex/laravel-tags/compare/v4.0.0...v4.0.1
[v4.0.0]: https://github.com/rinvex/laravel-tags/compare/v3.0.4...v4.0.0
[v3.0.4]: https://github.com/rinvex/laravel-tags/compare/v3.0.3...v3.0.4
[v3.0.3]: https://github.com/rinvex/laravel-tags/compare/v3.0.2...v3.0.3
[v3.0.2]: https://github.com/rinvex/laravel-tags/compare/v3.0.1...v3.0.2
[v3.0.1]: https://github.com/rinvex/laravel-tags/compare/v3.0.0...v3.0.1
[v3.0.0]: https://github.com/rinvex/laravel-tags/compare/v2.1.1...v3.0.0
[v2.1.1]: https://github.com/rinvex/laravel-tags/compare/v2.1.0...v2.1.1
[v2.1.0]: https://github.com/rinvex/laravel-tags/compare/v2.0.0...v2.1.0
[v2.0.0]: https://github.com/rinvex/laravel-tags/compare/v1.0.1...v2.0.0
[v1.0.1]: https://github.com/rinvex/laravel-tags/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/rinvex/laravel-tags/compare/v0.0.5...v1.0.0
[v0.0.5]: https://github.com/rinvex/laravel-tags/compare/v0.0.4...v0.0.5
[v0.0.4]: https://github.com/rinvex/laravel-tags/compare/v0.0.3...v0.0.4
[v0.0.3]: https://github.com/rinvex/laravel-tags/compare/v0.0.2...v0.0.3
[v0.0.2]: https://github.com/rinvex/laravel-tags/compare/v0.0.1...v0.0.2
