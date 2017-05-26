<?php

declare(strict_types=1);

namespace Rinvex\Taggable;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Collection;
use Spatie\EloquentSortable\Sortable;
use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Rinvex\Taggable\Tag.
 *
 * @property int            $id
 * @property array          $name
 * @property string         $slug
 * @property array          $description
 * @property int            $order
 * @property string         $group
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 * @property-read array     $category_list
 *
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereGroup($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag withGroup($group = null)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Taggable\Tag ordered($direction = 'asc')
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Tag extends Model implements Sortable
{
    use HasSlug;
    use SortableTrait;
    use HasTranslations;
    use ValidatingTrait;
    use CacheableEloquent;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = ['validating', 'validated'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * The sortable settings.
     *
     * @var array
     */
    public $sortable = ['order_column_name' => 'sort_order'];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('rinvex.taggable.tables.tags'));
        $this->setRules([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'slug' => 'required|alpha_dash|unique:'.config('rinvex.taggable.tables.tags').',slug',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function boot()
    {
        parent::boot();

        if (isset(static::$dispatcher)) {
            // Early auto generate slugs before validation
            static::$dispatcher->listen('eloquent.validating: '.static::class, function ($model, $event) {
                if (! $model->slug) {
                    if ($model->exists) {
                        $model->generateSlugOnCreate();
                    } else {
                        $model->generateSlugOnUpdate();
                    }
                }
            });
        }
    }

    /**
     * Get all attached models of the given class to the tag.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function entries(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'taggable', config('rinvex.taggable.tables.taggables'), 'tag_id', 'taggable_id');
    }

    /**
     * Set the translatable name attribute.
     *
     * @param string $value
     *
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = json_encode(! is_array($value) ? [app()->getLocale() => $value] : $value);
    }

    /**
     * Set the translatable description attribute.
     *
     * @param string $value
     *
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = ! empty($value) ? json_encode(! is_array($value) ? [app()->getLocale() => $value] : $value) : null;
    }

    /**
     * Enforce clean slugs.
     *
     * @param string $value
     *
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value);
    }

    /**
     * Get the options for generating the slug.
     *
     * @return \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->doNotGenerateSlugsOnUpdate()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug');
    }

    /**
     * Scope tags by given group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null                           $group
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithGroup(Builder $query, string $group = null): Builder
    {
        return $group ? $query->where('group', $group) : $query;
    }

    /**
     * Find many tags by name or create if not exists.
     *
     * @param array       $tags
     * @param string|null $group
     * @param string|null $locale
     *
     * @return \Illuminate\Support\Collection
     */
    public static function findManyByNameOrCreate(array $tags, string $group = null, string $locale = null): Collection
    {
        // Expects array of tag names
        return collect($tags)->map(function ($tag) use ($group, $locale) {
            return static::findByNameOrCreate($tag, $group, $locale);
        });
    }

    /**
     * Find tag by attribute or create if not exists.
     *
     * @param mixed       $name
     * @param string|null $locale
     * @param string|null $group
     *
     * @return static
     */
    public static function findByNameOrCreate(string $name, string $locale = null, string $group = null): Tag
    {
        $locale = $locale ?? app()->getLocale();

        return static::findByName($name, $locale) ?: static::createByName($name, $locale, $group);
    }

    /**
     * Find tag by name.
     *
     * @param string      $name
     * @param string|null $locale
     *
     * @return static|null
     */
    public static function findByName(string $name, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()->where("name->{$locale}", $name)->first();
    }

    /**
     * Create tag by name.
     *
     * @param string      $name
     * @param string|null $locale
     * @param string|null $group
     *
     * @return static
     */
    public static function createByName(string $name, string $locale = null, string $group = null): Tag
    {
        $locale = $locale ?? app()->getLocale();

        return static::create([
            'name' => [$locale => $name],
            'group' => $group,
        ]);
    }
}
