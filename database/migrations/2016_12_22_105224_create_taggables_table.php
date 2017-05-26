<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaggablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.taggable.tables.taggables'), function (Blueprint $table) {
            // Columns
            $table->integer('tag_id')->unsigned();
            $table->integer('taggable_id')->unsigned();
            $table->string('taggable_type');
            $table->timestamps();

            // Indexes
            $table->unique(['tag_id', 'taggable_id', 'taggable_type'], 'taggables_ids_type_unique');
            $table->foreign('tag_id')->references('id')->on('tags')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('rinvex.taggable.tables.taggables'));
    }
}
