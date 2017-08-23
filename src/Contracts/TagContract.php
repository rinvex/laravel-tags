<?php

declare(strict_types=1);

namespace Rinvex\Taggable\Contracts;

/**
 * Rinvex\Taggable\Contracts\TagContract.
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
interface TagContract
{
    //
}
