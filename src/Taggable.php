<?php

declare(strict_types=1);

namespace Rinvex\Taggable;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    /**
     * The Queued tags.
     *
     * @var array
     */
    protected $queuedTags = [];

    /**
     * Register a created model event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    abstract public static function created($callback);

    /**
     * Register a deleted model event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    abstract public static function deleted($callback);

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @param string      $related
     * @param string      $name
     * @param string|null $table
     * @param string|null $foreignKey
     * @param string|null $otherKey
     * @param bool        $inverse
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    abstract public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false);

    /**
     * Get tag class name.
     *
     * @return string
     */
    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * Get tags delimiter.
     *
     * @return string
     */
    public static function getTagsDelimiter(): string
    {
        return ',';
    }

    /**
     * Get all attached tags to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(static::getTagClassName(), 'taggable', config('rinvex.taggable.tables.taggables'), 'taggable_id', 'tag_id')->orderBy('order')->withTimestamps();
    }

    /**
     * Attach the given tag(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return void
     */
    public function setTagsAttribute($tags)
    {
        if (! $this->exists) {
            $this->queuedTags = $tags;

            return;
        }

        $this->tag($tags);
    }

    /**
     * Save attached tag(s) to the model.
     *
     * @return void
     */
    public static function bootTaggable()
    {
        static::created(function (Model $taggableModel) {
            if ($taggableModel->queuedTags) {
                $taggableModel->tag($taggableModel->queuedTags);

                $taggableModel->queuedTags = [];
            }
        });

        static::deleted(function (Model $taggableModel) {
            $taggableModel->retag(null);
        });
    }

    /**
     * Get the tag list.
     *
     * @param string $keyColumn
     *
     * @return array
     */
    public function tagList(string $keyColumn = 'slug'): array
    {
        return $this->tags()->pluck('name', $keyColumn)->toArray();
    }

    /**
     * Filter tags with type.
     *
     * @param string|null $type
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagsWithType(string $type = null): Collection
    {
        return $this->tags->filter(function (Tag $tag) use ($type) {
            return $tag->type === $type;
        });
    }

    /**
     * Scope query with all the given tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder         $query
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     * @param string                                        $column
     * @param string                                        $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllTags(Builder $query, $tags, string $column = 'slug', string $type = null): Builder
    {
        $tags = static::isTagsStringBased($tags) ? $tags : static::hydrateTags($tags)->pluck($column);

        collect($tags)->each(function ($tag) use ($query, $column, $type) {
            $query->whereHas('tags', function (Builder $query) use ($tag, $column, $type) {
                return $query->where($column, $tag)->when($type, function (Builder $query) use ($type) {
                    return $query->where('type', $type);
                });
            });
        });

        return $query;
    }

    /**
     * Scope query with any of the given tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder         $query
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     * @param string                                        $column
     * @param string                                        $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyTags(Builder $query, $tags, string $column = 'slug', string $type = null): Builder
    {
        $tags = static::isTagsStringBased($tags) ? $tags : static::hydrateTags($tags)->pluck($column);

        return $query->whereHas('tags', function (Builder $query) use ($tags, $column, $type) {
            $query->whereIn($column, (array) $tags)->when($type, function (Builder $query) use ($type) {
                return $query->where('type', $type);
            });
        });
    }

    /**
     * Scope query with any of the given tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder         $query
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     * @param string                                        $column
     * @param string                                        $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTags(Builder $query, $tags, string $column = 'slug', string $type = null): Builder
    {
        return static::scopeWithAnyTags($query, $tags, $column, $type);
    }

    /**
     * Scope query without any of the given tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder     $query
     * @param string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     * @param string                                    $column
     * @param string                                    $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutTags(Builder $query, $tags, string $column = 'slug', string $type = null): Builder
    {
        $tags = static::isTagsStringBased($tags) ? $tags : static::hydrateTags($tags)->pluck($column);

        return $query->whereDoesntHave('tags', function (Builder $query) use ($tags, $column, $type) {
            $query->whereIn($column, (array) $tags)->when($type, function (Builder $query) use ($type) {
                return $query->where('type', $type);
            });
        });
    }

    /**
     * Scope query without any tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutAnyTags(Builder $query): Builder
    {
        return $query->doesntHave('tags');
    }

    /**
     * Attach the given tag(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return $this
     */
    public function tag($tags)
    {
        static::setTags($tags, 'syncWithoutDetaching');

        return $this;
    }

    /**
     * Sync the given tag(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return $this
     */
    public function retag($tags)
    {
        static::setTags($tags, 'sync');

        return $this;
    }

    /**
     * Detach the given tag(s) from the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return $this
     */
    public function untag($tags)
    {
        static::setTags($tags, 'detach');

        return $this;
    }

    /**
     * Determine if the model has any the given tags.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return bool
     */
    public function hasTag($tags): bool
    {
        // Single tag slug
        if (is_string($tags)) {
            return $this->tags->contains('slug', $tags);
        }

        // Single tag id
        if (is_int($tags)) {
            return $this->tags->contains('id', $tags);
        }

        // Single tag model
        if ($tags instanceof Tag) {
            return $this->tags->contains('slug', $tags->slug);
        }

        // Array of tag slugs
        if (is_array($tags) && isset($tags[0]) && is_string($tags[0])) {
            return ! $this->tags->pluck('slug')->intersect($tags)->isEmpty();
        }

        // Array of tag ids
        if (is_array($tags) && isset($tags[0]) && is_int($tags[0])) {
            return ! $this->tags->pluck('id')->intersect($tags)->isEmpty();
        }

        // Collection of tag models
        if ($tags instanceof Collection) {
            return ! $tags->intersect($this->tags->pluck('slug'))->isEmpty();
        }

        return false;
    }

    /**
     * Determine if the model has any the given tags.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return bool
     */
    public function hasAnyTag($tags): bool
    {
        return static::hasTag($tags);
    }

    /**
     * Determine if the model has all of the given tags.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return bool
     */
    public function hasAllTags($tags): bool
    {
        // Single tag slug
        if (is_string($tags)) {
            return $this->tags->contains('slug', $tags);
        }

        // Single tag id
        if (is_int($tags)) {
            return $this->tags->contains('id', $tags);
        }

        // Single tag model
        if ($tags instanceof Tag) {
            return $this->tags->contains('slug', $tags->slug);
        }

        // Array of tag slugs
        if (is_array($tags) && isset($roles[0]) && is_string($tags[0])) {
            return $this->tags->pluck('slug')->count() === count($tags)
                   && $this->tags->pluck('slug')->diff($tags)->isEmpty();
        }

        // Array of tag ids
        if (is_array($tags) && isset($roles[0]) && is_string($tags[0])) {
            return $this->tags->pluck('id')->count() === count($tags)
                   && $this->tags->pluck('id')->diff($tags)->isEmpty();
        }

        // Collection of tag models
        if ($tags instanceof Collection) {
            return $this->tags->count() === $tags->count() && $this->tags->diff($tags)->isEmpty();
        }

        return false;
    }

    /**
     * Prepare tag list.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return mixed
     */
    protected function prepareTags($tags)
    {
        if (is_string($tags) && mb_strpos($tags, static::getTagsDelimiter()) !== false) {
            $delimiter = preg_quote(static::getTagsDelimiter(), '#');
            $tags = array_map('trim', preg_split("#[{$delimiter}]#", $tags, -1, PREG_SPLIT_NO_EMPTY));
        }

        return $tags;
    }

    /**
     * Set the given tag(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag|null $tags
     * @param string                                             $action
     *
     * @return void
     */
    protected function setTags($tags, string $action)
    {
        // Fix exceptional event name
        $event = $action === 'syncWithoutDetaching' ? 'attach' : $action;

        // Hydrate Tags
        $tags = static::hydrateTags($tags, in_array($event, ['sync', 'attach']))->pluck('id')->toArray();

        // Fire the tag syncing event
        static::$dispatcher->dispatch("rinvex.taggable.{$event}ing", [$this, $tags]);

        // Set tags
        $this->tags()->$action($tags);

        // Fire the tag synced event
        static::$dispatcher->dispatch("rinvex.taggable.{$event}ed", [$this, $tags]);
    }

    /**
     * Hydrate tags.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     * @param bool                                          $createMissing
     *
     * @return \Illuminate\Support\Collection
     */
    protected function hydrateTags($tags, bool $createMissing = false): Collection
    {
        $tags = static::prepareTags($tags);
        $isTagsStringBased = static::isTagsStringBased($tags);
        $isTagsIntBased = static::isTagsIntBased($tags);
        $field = $isTagsStringBased ? 'slug' : 'id';
        $className = static::getTagClassName();

        if ($isTagsStringBased && $createMissing) {
            return $className::findManyByNameOrCreate($tags);
        }

        return $isTagsStringBased || $isTagsIntBased ? $className::query()->whereIn($field, (array) $tags)->get() : collect($tags);
    }

    /**
     * Determine if the given tag(s) are string based.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return bool
     */
    protected function isTagsStringBased($tags)
    {
        return is_string($tags) || (is_array($tags) && isset($tags[0]) && is_string($tags[0]));
    }

    /**
     * Determine if the given tag(s) are integer based.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Taggable\Tag $tags
     *
     * @return bool
     */
    protected function isTagsIntBased($tags)
    {
        return is_int($tags) || (is_array($tags) && isset($tags[0]) && is_int($tags[0]));
    }
}
