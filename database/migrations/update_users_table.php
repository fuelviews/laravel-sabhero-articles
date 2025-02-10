<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
            $table->mediumText('bio')->nullable();
            $table->json('links')->nullable();
            $table->boolean('is_author')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users.bio');
        Schema::dropIfExists('users.links');
        Schema::dropIfExists('users.is_author');
    }
};
