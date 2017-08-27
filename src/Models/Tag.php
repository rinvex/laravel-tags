<?php

declare(strict_types=1);

namespace Rinvex\Taggable\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Collection;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Illuminate\Database\Eloquent\Builder;
use Rinvex\Support\Traits\HasTranslations;
use Rinvex\Support\Traits\ValidatingTrait;
use Rinvex\Taggable\Contracts\TagContract;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Rinvex\Taggable\Models\Tag.
 *
 * @property int            $id
 * @property string         $slug
 * @property array          $name
 * @property array          $description
 * @property int            $sort_order
 * @property string         $group
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag ordered($direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Taggable\Models\Tag withGroup($group = null)
 * @mixin \Eloquent
 */
class Tag extends Model implements TagContract, Sortable
{
    use HasSlug;
    use SortableTrait;
    use HasTranslations;
    use ValidatingTrait;
    use CacheableEloquent;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'sort_order',
        'group',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'slug' => 'string',
        'sort_order' => 'integer',
        'group' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'validating',
        'validated',
    ];

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
    public $sortable = [
        'order_column_name' => 'sort_order',
    ];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a
     * ValidationException if it fails validation.
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
            'slug' => 'required|alpha_dash|max:150|unique:'.config('rinvex.taggable.tables.tags').',slug',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:10000',
            'sort_order' => 'nullable|integer|max:10000000',
            'group' => 'nullable|string|max:150',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        // Early auto generate slugs before validation
        static::validating(function (self $tag) {
            if ($tag->exists && $tag->getSlugOptions()->generateSlugsOnUpdate) {
                $tag->generateSlugOnUpdate();
            } elseif (! $tag->exists && $tag->getSlugOptions()->generateSlugsOnCreate) {
                $tag->generateSlugOnCreate();
            }
        });
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
     * Enforce clean groups.
     *
     * @param string $value
     *
     * @return void
     */
    public function setGroupAttribute($value)
    {
        $this->attributes['group'] = str_slug($value);
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
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|null                           $group
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithGroup(Builder $builder, string $group = null): Builder
    {
        return $group ? $builder->where('group', $group) : $builder;
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
