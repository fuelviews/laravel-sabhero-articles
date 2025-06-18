<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('sabhero-article.tables.prefix').'categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 155)->unique();
            $table->string('slug', 155)->unique();
            $table->timestamps();
        });

        Schema::create(config('sabhero-article.tables.prefix').'authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->json('links')->nullable();
            $table->boolean('is_author')->default(false);
            $table->timestamps();
        });

        Schema::create(config('sabhero-article.tables.prefix').'posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('sub_title')->nullable();
            $table->longText('body');
            $table->enum('status', ['published', 'scheduled', 'pending'])->default('pending');
            $table->dateTime('published_at')->nullable();
            $table->dateTime('scheduled_for')->nullable();
            $table->string('feature_image_alt_text');
            $table->foreignId(config('sabhero-article.user.foreign_key'))
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create(config('sabhero-article.tables.prefix').'category_'.config('sabhero-article.tables.prefix').'post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')
                ->constrained(table: config('sabhero-article.tables.prefix').'posts')
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->constrained(table: config('sabhero-article.tables.prefix').'categories')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create(config('sabhero-article.tables.prefix').'tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 155)->unique();
            $table->timestamps();
        });

        Schema::create(config('sabhero-article.tables.prefix').'post_'.config('sabhero-article.tables.prefix').'tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')
                ->constrained(table: config('sabhero-article.tables.prefix').'posts')
                ->cascadeOnDelete();
            $table->foreignId('tag_id')
                ->constrained(table: config('sabhero-article.tables.prefix').'tags')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists(config('sabhero-article.tables.prefix').'category_'.config('sabhero-article.tables.prefix').'post');
        Schema::dropIfExists(config('sabhero-article.tables.prefix').'categories');
        Schema::dropIfExists(config('sabhero-article.tables.prefix').'authors');
        Schema::dropIfExists(config('sabhero-article.tables.prefix').'posts');
        Schema::dropIfExists(config('sabhero-article.tables.prefix').'tags');
        Schema::dropIfExists(config('sabhero-article.tables.prefix').'post_'.config('sabhero-article.tables.prefix').'tag');
        Schema::enableForeignKeyConstraints();
    }
};
