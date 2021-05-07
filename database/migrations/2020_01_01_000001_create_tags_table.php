<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('rinvex.tags.tables.tags'), function (Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->string('slug');
            $table->json('name');
            $table->json('description')->nullable();
            $table->mediumInteger('sort_order')->unsigned()->default(0);
            $table->string('group')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('rinvex.tags.tables.tags'));
    }
}
