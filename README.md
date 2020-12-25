# Rinvex Tags

**Rinvex Tags** is a polymorphic Laravel package, for tag management. You can tag any eloquent model with ease, and utilize the awesomeness of **[Sluggable](https://github.com/spatie/laravel-sluggable)**, and **[Translatable](https://github.com/spatie/laravel-translatable)** models out of the box.

[![Packagist](https://img.shields.io/packagist/v/rinvex/laravel-tags.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/rinvex/laravel-tags)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/rinvex/laravel-tags.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/rinvex/laravel-tags/)
[![Travis](https://img.shields.io/travis/rinvex/laravel-tags.svg?label=TravisCI&style=flat-square)](https://travis-ci.org/rinvex/laravel-tags)
[![StyleCI](https://styleci.io/repos/87597843/shield)](https://styleci.io/repos/87597843)
[![License](https://img.shields.io/packagist/l/rinvex/laravel-tags.svg?label=License&style=flat-square)](https://github.com/rinvex/laravel-tags/blob/develop/LICENSE)


## Installation

1. Install the package via composer:
    ```shell
    composer require rinvex/laravel-tags
    ```

2. Publish resources (migrations and config files):
    ```shell
    php artisan rinvex:publish:tags
    ```

3. Execute migrations via the following command:
    ```shell
    php artisan rinvex:migrate:tags
    ```

4. Done!


## Usage

To add tags support to your eloquent models simply use `\Rinvex\Tags\Traits\Taggable` trait.

### Manage your tags

Your tags are just normal [eloquent](https://laravel.com/docs/master/eloquent) models, so you can deal with it like so. There's few more methods added to tag models for your convenience, let's take a look:

```php
// Create new tag by name
app('rinvex.tags.tag')->createByName('My New Tag');

// Create new tag by name, group, and translation
app('rinvex.tags.tag')->createByName('The very new tag', 'blog', 'en');

// Find first tag by name
app('rinvex.tags.tag')->firstByName('My New Tag');

// Find first tag by name, group, and translation
app('rinvex.tags.tag')->firstByName('وسم جديد', 'board', 'ar');

// Find tag(s) by name
app('rinvex.tags.tag')->findByName('My New Tag');

// Find tag(s) by name, group, and translation
app('rinvex.tags.tag')->findByName('وسم جديد', 'board', 'ar');

// Find multiple tags by names array
app('rinvex.tags.tag')->findByName(['Tag One', 'Tag Two']);

// Find multiple tags by delimited names (tag delimiter is customizable)
app('rinvex.tags.tag')->findByName('First Tag, Second Tag, Third Tag');

// Find tag(s) by name or create if not exists
app('rinvex.tags.tag')->findByNameOrCreate('My Brand New Tag');

// Find tag(s) by name, group, and translation or create if not exists
app('rinvex.tags.tag')->findByNameOrCreate(['My Brand New Tag 2', 'My Brand New Tag 3']);
```

> **Notes:** 
> - **Rinvex Tags** extends and utilizes other awesome packages, to be translatable out of the box using [`spatie/laravel-translatable`](https://github.com/spatie/laravel-translatable), and for automatic Slugging it uses [`spatie/laravel-sluggable`](https://github.com/spatie/laravel-sluggable) packages. Them them out.
> - Both `findByName()` and `findByNameOrCreate()` methods accepts either one or more tags as their first argument, and always return a collection.

### Manage your taggable model

The API is intutive and very straightfarwad, so let's give it a quick look:

```php
// Get instance of your model
$post = new \App\Models\Post::find(1);

// Get attached tags collection
$post->tags;

// Get attached tags query builder
$post->tags();
```

You can attach tags in various ways:

```php
// Single tag id
$post->attachTags(1);

// Multiple tag IDs array
$post->attachTags([1, 2, 5]);

// Multiple tag IDs collection
$post->attachTags(collect([1, 2, 5]));

// Single tag model instance
$tagInstance = app('rinvex.tags.tag')->first();
$post->attachTags($tagInstance);

// Single tag name (created if not exists)
$post->attachTags('A very new tag');

// Multiple delimited tag names (use existing, create not existing)
$post->attachTags('First Tag, Second Tag, Third Tag');

// Multiple tag names array (use existing, create not existing)
$post->attachTags(['First Tag', 'Second Tag']);

// Multiple tag names collection (use existing, create not existing)
$post->attachTags(collect(['First Tag', 'Second Tag']));

// Multiple tag model instances
$tagInstances = app('rinvex.tags.tag')->whereIn('id', [1, 2, 5])->get();
$post->attachTags($tagInstances);
```

> **Notes:** 
> - The `attachTags()` method attach the given tags to the model without touching the currently attached tags, while there's the `syncTags()` method that can detach any records that's not in the given items, this method takes a second optional boolean parameter that's set detaching flag to `true` or `false`.
> - To detach model tags you can use the `detachTags()` method, which uses **exactly** the same signature as the `attachTags()` method, with additional feature of detaching all currently attached tags by passing null or nothing to that method as follows: `$post->detachTags();`.
> - You may have multiple tags with the same name and the same locale, in such case the first record found is used by default. This is intended by design to ensure a consistent behavior across all functionality whether you are attaching, detaching, or scoping model tags.

And as you may have expected, you can check if tags attached:

```php
// Single tag id
$post->hasAnyTags(1);

// Multiple tag IDs array
$post->hasAnyTags([1, 2, 5]);

// Multiple tag IDs collection
$post->hasAnyTags(collect([1, 2, 5]));

// Single tag model instance
$tagInstance = app('rinvex.tags.tag')->first();
$post->hasAnyTags($tagInstance);

// Single tag name
$post->hasAnyTags('A very new tag');

// Multiple delimited tag names
$post->hasAnyTags('First Tag, Second Tag, Third Tag');

// Multiple tag names array
$post->hasAnyTags(['First Tag', 'Second Tag']);

// Multiple tag names collection
$post->hasAnyTags(collect(['First Tag', 'Second Tag']));

// Multiple tag model instances
$tagInstances = app('rinvex.tags.tag')->whereIn('id', [1, 2, 5])->get();
$post->hasAnyTags($tagInstances);
```

> **Notes:** 
> - The `hasAnyTags()` method check if **ANY** of the given tags are attached to the model. It returns boolean `true` or `false` as a result.
> - Similarly the `hasAllTags()` method uses **exactly** the same signature as the `hasAnyTags()` method, but it behaves differently and performs a strict comparison to check if **ALL** of the given tags are attached.

### Advanced usage

#### Generate tag slugs

**Rinvex Tags** auto generates slugs and auto detect and insert default translation for you if not provided, but you still can pass it explicitly through normal eloquent `create` method, as follows:

```php
app('rinvex.tags.tag')->create(['name' => ['en' => 'My New Tag'], 'slug' => 'custom-tag-slug']);
```

> **Note:** Check **[Sluggable](https://github.com/spatie/laravel-sluggable)** package for further details.

#### Smart parameter detection

**Rinvex Tags** methods that accept list of tags are smart enough to handle almost all kinds of inputs as you've seen in the above examples. It will check input type and behave accordingly. 

#### Retrieve all models attached to the tag

You may encounter a situation where you need to get all models attached to certain tag, you do so with ease as follows:

```php
$tag = app('rinvex.tags.tag')->find(1);
$tag->entries(\App\Models\Post::class)->get();
```

#### Query scopes

Yes, **Rinvex Tags** shipped with few awesome query scopes for your convenience, usage example:

```php
// Single tag id
$post->withAnyTags(1)->get();

// Multiple tag IDs array
$post->withAnyTags([1, 2, 5])->get();

// Multiple tag IDs collection
$post->withAnyTags(collect([1, 2, 5]))->get();

// Single tag model instance
$tagInstance = app('rinvex.tags.tag')->first();
$post->withAnyTags($tagInstance)->get();

// Single tag name
$post->withAnyTags('A very new tag')->get();

// Multiple delimited tag names
$post->withAnyTags('First Tag, Second Tag, Third Tag');

// Multiple tag names array
$post->withAnyTags(['First Tag', 'Second Tag'])->get();

// Multiple tag names collection
$post->withAnyTags(collect(['First Tag', 'Second Tag']))->get();

// Multiple tag model instances
$tagInstances = app('rinvex.tags.tag')->whereIn('id', [1, 2, 5])->get();
$post->withAnyTags($tagInstances)->get();
```

> **Notes:** 
> - The `withAnyTags()` scope finds posts with **ANY** attached tags of the given. It returns normally a query builder, so you can chain it or call `get()` method for example to execute and get results.
> - Similarly there's few other scopes like `withAllTags()` that finds posts with **ALL** attached tags of the given, `withoutTags()` which finds posts without **ANY** attached tags of the given, and lastly `withoutAnyTags()` which find posts without **ANY** attached tags at all. All scopes are created equal, with same signature, and returns query builder.

#### Tag translations

Manage tag translations with ease as follows:

```php
$tag = app('rinvex.tags.tag')->find(1);

// Update name translations
$tag->setTranslation('name', 'en', 'New English Tag Name')->save();

// Alternatively you can use default eloquent update
$tag->update([
    'name' => [
        'en' => 'New Tag',
        'ar' => 'وسم جديد',
    ],
]);

// Get single tag translation
$tag->getTranslation('name', 'en');

// Get all tag translations
$tag->getTranslations('name');

// Get tag name in default locale
$tag->name;
```

> **Note:** Check **[Translatable](https://github.com/spatie/laravel-translatable)** package for further details.


## Changelog

Refer to the [Changelog](CHANGELOG.md) for a full history of the project.


## Support

The following support channels are available at your fingertips:

- [Chat on Slack](https://bit.ly/rinvex-slack)
- [Help on Email](mailto:help@rinvex.com)
- [Follow on Twitter](https://twitter.com/rinvex)


## Contributing & Protocols

Thank you for considering contributing to this project! The contribution guide can be found in [CONTRIBUTING.md](CONTRIBUTING.md).

Bug reports, feature requests, and pull requests are very welcome.

- [Versioning](CONTRIBUTING.md#versioning)
- [Pull Requests](CONTRIBUTING.md#pull-requests)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Feature Requests](CONTRIBUTING.md#feature-requests)
- [Git Flow](CONTRIBUTING.md#git-flow)


## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to [help@rinvex.com](help@rinvex.com). All security vulnerabilities will be promptly addressed.


## About Rinvex

Rinvex is a software solutions startup, specialized in integrated enterprise solutions for SMEs established in Alexandria, Egypt since June 2016. We believe that our drive The Value, The Reach, and The Impact is what differentiates us and unleash the endless possibilities of our philosophy through the power of software. We like to call it Innovation At The Speed Of Life. That’s how we do our share of advancing humanity.


## License

This software is released under [The MIT License (MIT)](LICENSE).

(c) 2016-2021 Rinvex LLC, Some rights reserved.
