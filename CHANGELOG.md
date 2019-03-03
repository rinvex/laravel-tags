# Rinvex Tags Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](CONTRIBUTING.md).


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

[v2.0.0]: https://github.com/rinvex/laravel-tags/compare/v1.0.1...v2.0.0
[v1.0.1]: https://github.com/rinvex/laravel-tags/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/rinvex/laravel-tags/compare/v0.0.5...v1.0.0
[v0.0.5]: https://github.com/rinvex/laravel-tags/compare/v0.0.4...v0.0.5
[v0.0.4]: https://github.com/rinvex/laravel-tags/compare/v0.0.3...v0.0.4
[v0.0.3]: https://github.com/rinvex/laravel-tags/compare/v0.0.2...v0.0.3
[v0.0.2]: https://github.com/rinvex/laravel-tags/compare/v0.0.1...v0.0.2
