<?php

declare(strict_types=1);

namespace Rinvex\Tags\Contracts;

/**
 * Rinvex\Tags\Contracts\TagContract.
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
interface TagContract
{
    //
}
