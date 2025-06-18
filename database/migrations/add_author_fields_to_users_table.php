<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('email');
            $table->text('bio')->nullable()->after('slug');
            $table->json('links')->nullable()->after('bio');
            $table->boolean('is_author')->default(false)->after('links');
        });

        // Update existing users with slugs derived from their names and set them as authors
        $users = DB::table('users')->whereNull('slug')->get();

        foreach ($users as $user) {
            $baseSlug = Str::slug($user->name);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure slug uniqueness
            while (DB::table('users')->where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'slug' => $slug,
                    'is_author' => true,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['slug', 'bio', 'links', 'is_author']);
        });
    }
};
