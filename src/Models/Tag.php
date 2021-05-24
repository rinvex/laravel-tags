<?php

declare(strict_types=1);

namespace Rinvex\Tags\Models;

use Rinvex\Tags\Traits\Taggable;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Collection;
use Rinvex\Support\Traits\HasSlug;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Rinvex\Support\Traits\HasTranslations;
use Rinvex\Support\Traits\ValidatingTrait;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Rinvex\Tags\Models\Tag.
 *
 * @property int                 $id
 * @property string              $slug
 * @property array               $name
 * @property array               $description
 * @property int                 $sort_order
 * @property string              $group
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag ordered($direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Tags\Models\Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tag extends Model implements Sortable
{
    use HasSlug;
    use SoftDeletes;
    use SortableTrait;
    use HasTranslations;
    use ValidatingTrait;

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
        $this->setTable(config('rinvex.tags.tables.tags'));
        $this->mergeRules([
            'slug' => 'required|alpha_dash|max:150|unique:'.config('rinvex.tags.tables.tags').',slug',
            'name' => 'required|string|strip_tags|max:150',
            'description' => 'nullable|string|max:32768',
            'sort_order' => 'nullable|integer|max:100000',
            'group' => 'nullable|string|strip_tags|max:150',
        ]);

        parent::__construct($attributes);
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
        return $this->morphedByMany($class, 'taggable', config('rinvex.tags.tables.taggables'), 'tag_id', 'taggable_id', 'id', 'id');
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
     * Get first tag(s) by name or create if not exists.
     *
     * @param mixed       $tags
     * @param string|null $group
     * @param string|null $locale
     *
     * @return \Illuminate\Support\Collection
     */
    public static function findByNameOrCreate($tags, string $group = null, string $locale = null): Collection
    {
        $locale = $locale ?? app()->getLocale();

        return collect(Taggable::parseDelimitedTags($tags))->map(function (string $tag) use ($group, $locale) {
            return static::firstByName($tag, $group, $locale) ?: static::createByName($tag, $group, $locale);
        });
    }

    /**
     * Find tag by name.
     *
     * @param mixed       $tags
     * @param string|null $group
     * @param string|null $locale
     *
     * @return \Illuminate\Support\Collection
     */
    public static function findByName($tags, string $group = null, string $locale = null): Collection
    {
        $locale = $locale ?? app()->getLocale();

        return collect(Taggable::parseDelimitedTags($tags))->map(function (string $tag) use ($group, $locale) {
            return ($exists = static::firstByName($tag, $group, $locale)) ? $exists->getKey() : null;
        })->filter()->unique();
    }

    /**
     * Get first tag by name.
     *
     * @param string      $tag
     * @param string|null $group
     * @param string|null $locale
     *
     * @return static|null
     */
    public static function firstByName(string $tag, string $group = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()->where("name->{$locale}", $tag)->when($group, function (Builder $builder) use ($group) {
            return $builder->where('group', $group);
        })->first();
    }

    /**
     * Create tag by name.
     *
     * @param string      $tag
     * @param string|null $locale
     * @param string|null $group
     *
     * @return static
     */
    public static function createByName(string $tag, string $group = null, string $locale = null): self
    {
        $locale = $locale ?? app()->getLocale();

        return static::create([
            'name' => [$locale => $tag],
            'group' => $group,
        ]);
    }
}
