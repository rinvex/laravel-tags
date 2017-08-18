<?php

declare(strict_types=1);

return [

    // Taggable Database Tables
    'tables' => [

        'tags' => 'tags',
        'taggables' => 'taggables',

    ],

    // Taggable Models
    'models' => [
        'tag' => \Rinvex\Taggable\Models\Tag::class,
    ],

];
