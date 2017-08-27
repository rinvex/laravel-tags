# Rinvex Taggable

**Rinvex Taggable** is a polymorphic Laravel package, for tag management. You can tag any eloquent model with ease, and utilize the awesomeness of **[Sluggable](https://github.com/spatie/laravel-sluggable)**, and **[Translatable](https://github.com/spatie/laravel-translatable)** models out of the box.

[![Packagist](https://img.shields.io/packagist/v/rinvex/taggable.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/rinvex/taggable)
[![VersionEye Dependencies](https://img.shields.io/versioneye/d/php/rinvex:taggable.svg?label=Dependencies&style=flat-square)](https://www.versioneye.com/php/rinvex:taggable/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/rinvex/taggable.svg?label=Scrutinizer&style=flat-square)](https://scrutinizer-ci.com/g/rinvex/taggable/)
[![Code Climate](https://img.shields.io/codeclimate/github/rinvex/taggable.svg?label=CodeClimate&style=flat-square)](https://codeclimate.com/github/rinvex/taggable)
[![Travis](https://img.shields.io/travis/rinvex/taggable.svg?label=TravisCI&style=flat-square)](https://travis-ci.org/rinvex/taggable)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/898fbbb8-7104-4c58-bb85-d9ada4afe481.svg?label=SensioLabs&style=flat-square)](https://insight.sensiolabs.com/projects/898fbbb8-7104-4c58-bb85-d9ada4afe481)
[![StyleCI](https://styleci.io/repos/87597843/shield)](https://styleci.io/repos/87597843)
[![License](https://img.shields.io/packagist/l/rinvex/taggable.svg?label=License&style=flat-square)](https://github.com/rinvex/taggable/blob/develop/LICENSE)


## Installation

1. Install the package via composer:
    ```shell
    composer require rinvex/taggable
    ```

2. Execute migrations via the following command:
    ```
    php artisan rinvex:migrate:taggable
    ```

3. Done!


## Usage

### Create Your Model

Simply create a new eloquent model, and use `\Rinvex\Taggable\Traits\Taggable` trait:

```php
namespace App\Models;

use Rinvex\Taggable\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Post extends Model
{
    use Taggable;
}
```

### Manage Your Tags

```php
// Create a new tag by name
app('rinvex.taggable.tag')->createByName('My New Tag');

// Create a new tag by translation and type
app('rinvex.taggable.tag')->createByName('The very new tag', 'en', 'blog');

// Get existing tag by name
app('rinvex.taggable.tag')->findByName('My New Tag');

// Get existing tag by translation
app('rinvex.taggable.tag')->findByName('وسم جديد', 'ar');

// Find tag by name or create if not exists
app('rinvex.taggable.tag')->findByNameOrCreate('My Brand New Tag');

// Find many tags by name or create if not exists
app('rinvex.taggable.tag')->findManyByNameOrCreate(['My Brand New Tag 2', 'My Brand New Tag 3']);
```

> **Notes:** since **Rinvex Taggable** extends and utilizes other awesome packages, checkout the following documentations for further details:
> - Automatic Slugging using [`spatie/laravel-sluggable`](https://github.com/spatie/laravel-sluggable)
> - Translatable out of the box using [`spatie/laravel-translatable`](https://github.com/spatie/laravel-translatable)

### Manage Your Taggable Model

The API is intutive and very straightfarwad, so let's give it a quick look:

```php
// Instantiate your model
$post = new \App\Models\Post();

// Attach given tags to the model
$post->tag(['my-new-tag', 'my-brand-new-tag']);

// Detach given tags from the model
$post->untag(['my-new-tag']);

// Sync given tags with the model (remove attached tags and reattach given ones)
$post->retag(['my-new-tag', 'my-brand-new-tag']);

// Remove all attached tags
$post->tags()->detach();

// Alternatively you can remove all attached tags as follows
$post->retag(null);

// Get attached tags collection
$post->tags;

// Get attached tags query builder
$post->tags();

// Check model if has any given tags
$post->hasTag(['my-new-tag', 'my-brand-new-tag']);

// Check model if has any given tags
$post->hasAllTags(['my-new-tag', 'my-brand-new-tag']);
```

### Advanced Usage

#### Generate Tag Slugs

**Rinvex Taggable** auto generates slugs and auto detect and insert default translation for you, but you still can pass it explicitly through normal eloquent `create` method, as follows:

```php
app('rinvex.taggable.tag')->create(['name' => ['en' => 'My New Tag'], 'slug' => 'custom-tag-slug']);
```

#### Smart Parameter Detection

All taggable methods that accept list of tags are smart enough to handle almost all kind of inputs, for example you can pass single tag slug, single tag id, single tag model, an array of tag slugs, an array of tag ids, or a collection of tag models. It will check input type and behave accordingly. Example:

```php
$post->hasTag(1);
$post->hasTag([1,2,4]);
$post->hasTag('my-new-tag');
$post->hasTag(['my-new-tag', 'my-brand-new-tag']);
$post->hasTag(app('rinvex.taggable.tag')->where('slug', 'my-new-tag')->first());
$post->hasTag(app('rinvex.taggable.tag')->whereIn('id', [5,6,7])->get());
```
**Rinvex Taggable** can understand any of the above parameter syntax and interpret it correctly, same for other methods in this package.

#### Retrieve All Models Attached To The Tag

It's very easy to get all models attached to certain tag as follows:

```php
$tag = app('rinvex.taggable.tag')->find(1);
$tag->entries(\App\Models\Post::class);
```

#### Fired Events

You can listen to the following events fired whenever there's an action on tags:

- rinvex.taggable.attaching
- rinvex.taggable.attached
- rinvex.taggable.detaching
- rinvex.taggable.detached
- rinvex.taggable.syncing
- rinvex.taggable.synced

#### Query Scopes

Yes, **Rinvex Taggable** shipped with few awesome query scopes for your convenience, usage example:

```php
// Get models with all given tags
$postsWithAllTags = \App\Models\Post::withAllTags(['my-new-tag', 'my-brand-new-tag'])->get();

// Get models with any given tags
$postsWithAnyTags = \App\Models\Post::withAnyTags(['my-new-tag', 'my-brand-new-tag'])->get();

// Get models without tags
$postsWithoutTags = \App\Models\Post::withoutTags(['my-new-tag', 'my-brand-new-tag'])->get();

// Get models without any tags
$postsWithoutAnyTags = \App\Models\Post::withoutAnyTags()->get();
```

#### Tag Translations

Manage tag translations with ease as follows:

```php
$tag = app('rinvex.taggable.tag')->find(1);

// Set tag translation
$tag->setTranslation('name', 'en', 'Name in English');

// Get tag translation
$tag->setTranslation('name', 'en');

// Get tag name in default locale
$tag->name;
```


## Changelog

Refer to the [Changelog](CHANGELOG.md) for a full history of the project.


## Support

The following support channels are available at your fingertips:

- [Chat on Slack](http://chat.rinvex.com)
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

(c) 2016-2017 Rinvex LLC, Some rights reserved.
