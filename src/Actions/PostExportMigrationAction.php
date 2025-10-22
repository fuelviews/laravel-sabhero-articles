<?php

namespace Fuelviews\SabHeroArticles\Actions;

use Filament\Notifications\Notification;
use Fuelviews\SabHeroArticles\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Post Export Migration Action
 *
 * Exports posts as a migration file package that can be copied to another project.
 * Includes migration file, markdown files with frontmatter, images, and installation instructions.
 */
class PostExportMigrationAction
{
    /**
     * Execute the export action
     *
     * @param  Collection<int, Post>  $posts
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function execute(Collection $posts)
    {
        try {
            // Create export directory structure
            $timestamp = date('Y_m_d_His');
            $exportDir = storage_path("app/sabhero-articles/temp/posts-migration-export-{$timestamp}");
            $postsDir = "{$exportDir}/posts";
            $markdownDir = "{$postsDir}/markdown";
            $imagesDir = "{$exportDir}/images";

            File::makeDirectory($exportDir, 0755, true);
            File::makeDirectory($markdownDir, 0755, true);
            File::makeDirectory($imagesDir, 0755, true);

            // Export markdown files with frontmatter
            $this->exportMarkdown($posts, $markdownDir);

            // Export post images
            $this->exportImages($posts, $imagesDir);

            // Generate migration file with user data
            $migrationContent = $this->generateMigration($posts);
            File::put("{$exportDir}/{$timestamp}_populate_exported_posts.php", $migrationContent);

            // Create README
            $readmeContent = $this->generateReadme($timestamp, $posts->count());
            File::put("{$exportDir}/README.md", $readmeContent);

            // Create ZIP file with descriptive naming
            $domain = str_replace(['http://', 'https://', 'www.', '.', '-'], ['', '', '', '_', '_'], request()->getHost());
            $postCount = $posts->count();
            $estTime = now()->tz('America/New_York');
            $date = $estTime->format('Y_m_d');
            $time = strtolower($estTime->format('h_i_a'));
            $exportFileName = "{$domain}_{$postCount}_posts_migration_export_on_{$date}_at_{$time}.zip";
            $zipFilePath = storage_path("app/sabhero-articles/{$exportFileName}");
            $this->createZip($zipFilePath, $exportDir);

            Notification::make()
                ->title('Posts migration exported successfully')
                ->body("Exported {$posts->count()} posts with markdown files and images")
                ->success()
                ->send();

            return response()->download($zipFilePath);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Export failed')
                ->body('An error occurred: '.$e->getMessage())
                ->danger()
                ->send();

            return back();
        }
    }

    /**
     * Export post feature images and user avatars to public/images
     */
    protected function exportImages(Collection $posts, string $imagesDir): void
    {
        $exportedCount = 0;
        $skippedCount = 0;
        $mediaDisk = config('sabhero-articles.media.disk', 'public');
        $exportedAvatars = [];

        foreach ($posts as $post) {
            // Export post feature image
            $media = $post->getFirstMedia('post_feature_image');

            if ($media) {
                try {
                    $filename = Str::slug($post->slug)."-{$media->id}.{$media->extension}";
                    $targetPath = "{$imagesDir}/{$filename}";
                    $mediaPath = $media->getPath();

                    // If using cloud storage, download to temp
                    if (! file_exists($mediaPath)) {
                        $diskPath = str_replace(Storage::disk($mediaDisk)->path(''), '', $mediaPath);

                        if (Storage::disk($mediaDisk)->exists($diskPath)) {
                            $fileContents = Storage::disk($mediaDisk)->get($diskPath);
                            file_put_contents($targetPath, $fileContents);
                            $exportedCount++;

                            continue;
                        }
                    }

                    // Copy local file to export directory
                    if (file_exists($mediaPath)) {
                        copy($mediaPath, $targetPath);
                        $exportedCount++;
                    } else {
                        Log::warning("Image file not found for post: {$post->title}", [
                            'post_id' => $post->id,
                            'media_id' => $media->id,
                            'path' => $mediaPath,
                        ]);
                        $skippedCount++;
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to export image for post: {$post->title}", [
                        'post_id' => $post->id,
                        'error' => $e->getMessage(),
                    ]);
                    $skippedCount++;
                }
            }

            // Export user avatar (avoid duplicates)
            if ($post->user) {
                $userId = $post->user->id;

                if (! in_array($userId, $exportedAvatars)) {
                    $avatar = $post->user->getFirstMedia('avatar');

                    if ($avatar) {
                        try {
                            $avatarSlug = Str::slug($post->user->slug ?? $post->user->name);
                            $filename = "{$avatarSlug}-avatar-{$avatar->id}.{$avatar->extension}";
                            $targetPath = "{$imagesDir}/{$filename}";
                            $avatarPath = $avatar->getPath();

                            // If using cloud storage, download to temp
                            if (! file_exists($avatarPath)) {
                                $diskPath = str_replace(Storage::disk($mediaDisk)->path(''), '', $avatarPath);

                                if (Storage::disk($mediaDisk)->exists($diskPath)) {
                                    $fileContents = Storage::disk($mediaDisk)->get($diskPath);
                                    file_put_contents($targetPath, $fileContents);
                                    $exportedCount++;
                                    $exportedAvatars[] = $userId;

                                    continue;
                                }
                            }

                            // Copy local file to export directory
                            if (file_exists($avatarPath)) {
                                copy($avatarPath, $targetPath);
                                $exportedCount++;
                                $exportedAvatars[] = $userId;
                            }
                        } catch (\Exception $e) {
                            Log::warning("Failed to export avatar for user: {$post->user->name}", [
                                'user_id' => $post->user->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }

        Log::info("Image export complete", [
            'post_images' => $exportedCount - count($exportedAvatars),
            'avatars' => count($exportedAvatars),
            'total_exported' => $exportedCount,
            'skipped' => $skippedCount,
        ]);
    }

    /**
     * Export post content as markdown files with YAML frontmatter
     */
    protected function exportMarkdown(Collection $posts, string $markdownDir): void
    {
        $exportedCount = 0;
        $exportOrder = 0;

        foreach ($posts as $post) {
            try {
                $exportOrder++;
                $filename = "{$post->slug}.md";
                $filePath = "{$markdownDir}/{$filename}";

                // Build frontmatter (user info will be in migration, not frontmatter)
                $frontmatter = [
                    'export_order' => $exportOrder,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'sub_title' => $post->sub_title,
                    'status' => $post->status->value ?? 'published',
                    'published_at' => $post->published_at?->format('Y-m-d H:i:s'),
                    'scheduled_for' => $post->scheduled_for?->format('Y-m-d H:i:s'),
                    'user_email' => $post->user?->email,
                    'categories' => $post->categories->pluck('name')->toArray(),
                    'tags' => $post->tags->pluck('name')->toArray(),
                    'post_feature_image_alt_text' => $post->post_feature_image_alt_text,
                ];

                // Get post feature image filename if exists
                $media = $post->getFirstMedia('post_feature_image');
                if ($media) {
                    $frontmatter['post_feature_image'] = Str::slug($post->slug)."-{$media->id}.{$media->extension}";
                }

                // Build markdown content with YAML frontmatter
                $content = "---\n";
                foreach ($frontmatter as $key => $value) {
                    if ($value === null) {
                        continue;
                    }

                    if (is_array($value)) {
                        if (empty($value)) {
                            $content .= "{$key}: []\n";
                        } else {
                            $content .= "{$key}:\n";
                            foreach ($value as $item) {
                                // Proper YAML string escaping
                                $escapedItem = str_replace(["\\", '"'], ["\\\\", '\\"'], $item);
                                $content .= "  - \"{$escapedItem}\"\n";
                            }
                        }
                    } else {
                        // Proper YAML string escaping for scalar values
                        $escapedValue = str_replace(["\\", '"'], ["\\\\", '\\"'], $value);
                        $content .= "{$key}: \"{$escapedValue}\"\n";
                    }
                }
                $content .= "---\n\n";
                $content .= $post->body;

                // Write markdown file
                File::put($filePath, $content);
                $exportedCount++;

            } catch (\Exception $e) {
                Log::warning("Failed to export markdown for post: {$post->title}", [
                    'post_id' => $post->id,
                    'slug' => $post->slug,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("Markdown export complete", [
            'exported' => $exportedCount,
            'total_posts' => $posts->count(),
        ]);
    }

    /**
     * Generate PHP code for creating users
     */
    protected function generateUserCreationCode(array $users): string
    {
        if (empty($users)) {
            return '        // No users to create';
        }

        $code = '';
        foreach ($users as $userData) {
            $name = addslashes($userData['name']);
            $email = $userData['email'];
            $slug = $userData['slug'];
            $bio = addslashes($userData['bio'] ?? '');
            $isAuthor = $userData['is_author'] ? 'true' : 'false';
            $avatar = $userData['avatar'];

            // Generate random password
            $randomPassword = Str::random(32);

            // Handle links (could be JSON or array)
            $linksCode = 'null';
            if (! empty($userData['links'])) {
                if (is_string($userData['links'])) {
                    $linksCode = "'" . addslashes($userData['links']) . "'";
                } elseif (is_array($userData['links'])) {
                    $linksCode = "json_encode(" . var_export($userData['links'], true) . ")";
                }
            }

            $code .= <<<USERCODE

        // Create or update user: {$name}
        \$existingUser = DB::table('users')->where('email', '{$email}')->first();
        if (\$existingUser) {
            \$userId_{$slug} = \$existingUser->id;
            // Update existing user with required fields
            DB::table('users')->where('id', \$userId_{$slug})->update([
                'slug' => '{$slug}',
                'bio' => '{$bio}',
                'is_author' => {$isAuthor},
                'links' => {$linksCode},
                'updated_at' => now(),
            ]);
        } else {
            \$userId_{$slug} = DB::table('users')->insertGetId([
                'name' => '{$name}',
                'email' => '{$email}',
                'password' => bcrypt('{$randomPassword}'),
                'slug' => '{$slug}',
                'bio' => '{$bio}',
                'is_author' => {$isAuthor},
                'links' => {$linksCode},
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


USERCODE;

            // Add avatar attachment code if avatar exists
            if ($avatar) {
                $code .= <<<AVATARCODE
        // Add avatar for user: {$name}
        \$user_{$slug} = \$userModel::find(\$userId_{$slug});
        if (\$user_{$slug}) {
            \$avatarPath = public_path('images/{$avatar}');

            if (file_exists(\$avatarPath)) {
                try {
                    \$mediaDisk = config('sabhero-articles.media.disk', 'public');
                    \$user_{$slug}->clearMediaCollection('avatar');
                    \$user_{$slug}->addMedia(\$avatarPath)
                        ->preservingOriginal()
                        ->withResponsiveImages()
                        ->toMediaCollection('avatar', \$mediaDisk);

                    Log::info("Added avatar for user: {$name}");
                } catch (\Exception \$e) {
                    Log::warning("Could not attach avatar for user {$name}: " . \$e->getMessage());
                }
            } else {
                Log::warning("Avatar file not found for user: {$name}");
            }
        }


AVATARCODE;
            }
        }

        return $code;
    }

    /**
     * Generate migration file content
     */
    protected function generateMigration(Collection $posts): string
    {
        // Extract unique users and their data
        $users = [];
        foreach ($posts as $post) {
            if ($post->user && ! isset($users[$post->user->email])) {
                $avatar = $post->user->getFirstMedia('avatar');
                $avatarSlug = Str::slug($post->user->slug ?? $post->user->name);

                $users[$post->user->email] = [
                    'name' => $post->user->name,
                    'email' => $post->user->email,
                    'slug' => $post->user->slug ?? Str::slug($post->user->name),
                    'bio' => $post->user->bio,
                    'is_author' => $post->user->is_author ?? true,
                    'links' => $post->user->links,
                    'avatar' => $avatar ? "{$avatarSlug}-avatar-{$avatar->id}.{$avatar->extension}" : null,
                ];
            }
        }

        // Generate user creation code
        $userCreationCode = $this->generateUserCreationCode($users);

        $postCount = $posts->count();

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Seeds posts exported from another installation.
     * Markdown files should be placed in database/posts/markdown/
     * Images should be placed in public/images/
     *
     * This migration will:
     * - Create or update users/authors with all fields (name, email, slug, bio, links, is_author)
     * - Generate random passwords for new users
     * - Attach user avatar images from public/images/
     * - Read all .md files from database/posts/markdown/
     * - Parse YAML frontmatter for post metadata
     * - Create posts with all content and metadata
     * - Create/attach categories and tags
     * - Attach post feature images from public/images/
     * - Skip posts that already exist (by slug)
     */
    public function up(): void
    {
        \$userModel = config('sabhero-articles.user.model');
        \$tablePrefix = config('sabhero-articles.tables.prefix');
        \$postsTable = \$tablePrefix . 'posts';
        \$categoriesTable = \$tablePrefix . 'categories';
        \$tagsTable = \$tablePrefix . 'tags';
        // Pivot tables use prefix twice (e.g. articles_category_articles_post)
        \$categoryPostTable = \$tablePrefix . 'category_' . \$tablePrefix . 'post';
        \$postTagTable = \$tablePrefix . 'post_' . \$tablePrefix . 'tag';

        echo "\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "  Creating/Updating Users\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "\\n";

{$userCreationCode}

        echo "\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "  Importing {$postCount} Posts from Markdown Files\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "\\n";

        \$markdownPath = database_path('posts/markdown');

        // Get all markdown files
        \$markdownFiles = glob(\$markdownPath . '/*.md');

        if (empty(\$markdownFiles)) {
            echo "\\n";
            echo "✗ No markdown files found in {\$markdownPath}\\n";
            echo "\\n";
            Log::error('No markdown files found for post import', [
                'path' => \$markdownPath,
            ]);
            return;
        }

        // Parse all markdown files and extract export_order for sorting
        \$postsToImport = [];
        foreach (\$markdownFiles as \$markdownFile) {
            \$postData = \$this->parseMarkdownFile(\$markdownFile);
            if (\$postData) {
                \$postsToImport[] = [
                    'file' => \$markdownFile,
                    'data' => \$postData,
                    'order' => \$postData['export_order'] ?? 9999,
                ];
            }
        }

        // Sort by export_order in descending order (highest/newest first)
        usort(\$postsToImport, function(\$a, \$b) {
            return \$b['order'] <=> \$a['order'];
        });

        echo "  ℹ Posts will be imported in reverse order (newest first)\\n";
        echo "\\n";

        \$imported = 0;
        \$skipped = 0;
        \$errors = 0;

        foreach (\$postsToImport as \$postItem) {
            \$markdownFile = \$postItem['file'];
            \$postData = \$postItem['data'];

            try {
                // ─────────────────────────────────────────────────────────
                // Step 1: Check if post already exists
                // ─────────────────────────────────────────────────────────
                \$existingPost = DB::table(\$postsTable)->where('slug', \$postData['slug'])->first();
                if (\$existingPost) {
                    echo "  ℹ Skipping '{\$postData['slug']}' - already exists\\n";
                    \$skipped++;
                    continue;
                }

                // ─────────────────────────────────────────────────────────
                // Step 2: Find user by email
                // ─────────────────────────────────────────────────────────
                \$user = null;

                if (!empty(\$postData['user_email'])) {
                    \$user = \$userModel::where('email', \$postData['user_email'])->first();

                    if (!\$user) {
                        Log::warning("User not found by email: {\$postData['user_email']}", [
                            'post_slug' => \$postData['slug'],
                        ]);
                    }
                }

                // Fallback to first available user
                if (!\$user) {
                    \$user = \$userModel::first();

                    if (!\$user) {
                        echo "  ✗ No users found in database. Create a user first.\\n";
                        Log::error('No users available for post import', [
                            'post_slug' => \$postData['slug'],
                        ]);
                        \$errors++;
                        continue;
                    }
                }

                // ─────────────────────────────────────────────────────────
                // Step 3: Create post
                // ─────────────────────────────────────────────────────────
                \$postId = DB::table(\$postsTable)->insertGetId([
                    'slug' => \$postData['slug'],
                    'title' => \$postData['title'],
                    'sub_title' => \$postData['sub_title'] ?? null,
                    'body' => \$postData['body'],
                    'status' => \$postData['status'] ?? 'published',
                    'published_at' => \$postData['published_at'] ?? null,
                    'scheduled_for' => \$postData['scheduled_for'] ?? null,
                    'user_id' => \$user->id,
                    'post_feature_image_alt_text' => \$postData['post_feature_image_alt_text'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // ─────────────────────────────────────────────────────────
                // Step 4: Attach categories
                // ─────────────────────────────────────────────────────────
                if (!empty(\$postData['categories'])) {
                    foreach (\$postData['categories'] as \$categoryName) {
                        \$category = DB::table(\$categoriesTable)->updateOrInsert(
                            ['slug' => \\Illuminate\\Support\\Str::slug(\$categoryName)],
                            [
                                'name' => \$categoryName,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                        // Get the category ID
                        \$categoryId = DB::table(\$categoriesTable)
                            ->where('name', \$categoryName)
                            ->value('id');

                        // Attach to post (avoid duplicates)
                        DB::table(\$categoryPostTable)->insertOrIgnore([
                            'post_id' => \$postId,
                            'category_id' => \$categoryId,
                        ]);
                    }
                }

                // ─────────────────────────────────────────────────────────
                // Step 5: Attach tags
                // ─────────────────────────────────────────────────────────
                if (!empty(\$postData['tags'])) {
                    foreach (\$postData['tags'] as \$tagName) {
                        DB::table(\$tagsTable)->updateOrInsert(
                            ['slug' => \\Illuminate\\Support\\Str::slug(\$tagName)],
                            [
                                'name' => \$tagName,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                        // Get the tag ID
                        \$tagId = DB::table(\$tagsTable)
                            ->where('name', \$tagName)
                            ->value('id');

                        // Attach to post (avoid duplicates)
                        DB::table(\$postTagTable)->insertOrIgnore([
                            'post_id' => \$postId,
                            'tag_id' => \$tagId,
                        ]);
                    }
                }

                // ─────────────────────────────────────────────────────────
                // Step 6: Attach feature image if exists
                // ─────────────────────────────────────────────────────────
                if (!empty(\$postData['post_feature_image'])) {
                    \$imagePath = public_path('images/' . \$postData['post_feature_image']);

                    if (file_exists(\$imagePath)) {
                        try {
                            \$post = \\Fuelviews\\SabHeroArticles\\Models\\Post::find(\$postId);

                            if (\$post) {
                                \$mediaDisk = config('sabhero-articles.media.disk');
                                \$post->addMedia(\$imagePath)
                                    ->preservingOriginal()
                                    ->withResponsiveImages()
                                    ->toMediaCollection('post_feature_image', \$mediaDisk);
                            }
                        } catch (\\Exception \$e) {
                            Log::warning("Failed to attach image for post: {\$postData['title']}", [
                                'post_id' => \$postId,
                                'image_path' => \$imagePath,
                                'error' => \$e->getMessage(),
                            ]);
                            echo "  ⚠ Failed to attach image: {\$postData['post_feature_image']}\\n";
                        }
                    } else {
                        Log::warning("Image file not found for post: {\$postData['title']}", [
                            'post_id' => \$postId,
                            'expected_path' => \$imagePath,
                        ]);
                        echo "  ⚠ Image not found: {\$postData['post_feature_image']}\\n";
                    }
                }

                echo "  ✓ Imported: {\$postData['title']}\\n";
                \$imported++;

            } catch (\\Exception \$e) {
                Log::error("Failed to import post from: " . basename(\$markdownFile), [
                    'file' => \$markdownFile,
                    'error' => \$e->getMessage(),
                    'trace' => \$e->getTraceAsString(),
                ]);
                echo "  ✗ Error importing '" . basename(\$markdownFile) . "': {\$e->getMessage()}\\n";
                \$errors++;
            }
        }

        echo "\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "  Import Complete\\n";
        echo "────────────────────────────────────────────────────────────────\\n";
        echo "  ✓ Imported: {\$imported}\\n";
        echo "  ℹ Skipped:  {\$skipped}\\n";
        echo "  ✗ Errors:   {\$errors}\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "\\n";

        Log::info('Post migration import completed', [
            'imported' => \$imported,
            'skipped' => \$skipped,
            'errors' => \$errors,
        ]);
    }

    /**
     * Parse markdown file with YAML frontmatter
     */
    protected function parseMarkdownFile(string \$filePath): ?array
    {
        try {
            \$content = file_get_contents(\$filePath);

            // Check for frontmatter
            if (!preg_match('/^---\\s*\\n(.*?)\\n---\\s*\\n(.*)$/s', \$content, \$matches)) {
                Log::warning("No frontmatter found in markdown file", [
                    'file' => \$filePath,
                ]);
                return null;
            }

            \$frontmatterYaml = \$matches[1];
            \$body = trim(\$matches[2]);

            // Parse YAML frontmatter
            \$frontmatter = Yaml::parse(\$frontmatterYaml);

            if (!isset(\$frontmatter['slug']) || !isset(\$frontmatter['title'])) {
                Log::warning("Missing required fields (slug/title) in frontmatter", [
                    'file' => \$filePath,
                ]);
                return null;
            }

            return array_merge(\$frontmatter, ['body' => \$body]);

        } catch (\\Exception \$e) {
            Log::error("Failed to parse markdown file", [
                'file' => \$filePath,
                'error' => \$e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Reverse the migrations.
     *
     * WARNING: This will delete all posts imported by this migration.
     * Posts are identified by reading markdown files and matching slugs.
     */
    public function down(): void
    {
        \$markdownPath = database_path('posts/markdown');
        \$markdownFiles = glob(\$markdownPath . '/*.md');

        if (empty(\$markdownFiles)) {
            echo "\\n";
            echo "✗ No markdown files found - cannot determine which posts to delete\\n";
            echo "\\n";
            return;
        }

        echo "\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "  Rolling Back Post Migration Import\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "\\n";

        \$deleted = 0;

        foreach (\$markdownFiles as \$markdownFile) {
            try {
                \$postData = \$this->parseMarkdownFile(\$markdownFile);

                if (!\$postData || empty(\$postData['slug'])) {
                    continue;
                }

                \$slug = \$postData['slug'];
                \$post = \\Fuelviews\\SabHeroArticles\\Models\\Post::where('slug', \$slug)->first();

                if (\$post) {
                    // Delete media files
                    \$post->clearMediaCollection('post_feature_image');

                    // Delete post (cascade will handle pivot table entries)
                    \$post->delete();

                    echo "  ✓ Deleted: {\$slug}\\n";
                    \$deleted++;

                    Log::info("Rolled back post: {\$slug}");
                }
            } catch (\\Exception \$e) {
                Log::error("Failed to rollback post from: " . basename(\$markdownFile), [
                    'error' => \$e->getMessage(),
                ]);
                echo "  ✗ Error deleting from '" . basename(\$markdownFile) . "': {\$e->getMessage()}\\n";
            }
        }

        echo "\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "  Rollback Complete - Deleted {\$deleted} posts\\n";
        echo "════════════════════════════════════════════════════════════════\\n";
        echo "\\n";

        Log::info('Post migration rollback completed', [
            'deleted' => \$deleted,
        ]);
    }
};
PHP;
    }

    /**
     * Generate README file content
     */
    protected function generateReadme(string $timestamp, int $postCount): string
    {
        return <<<MD
# Posts Migration Export

This package contains {$postCount} exported posts from SabHero Articles that can be imported into another Laravel project.

## Contents

- `{$timestamp}_populate_exported_posts.php` - Migration file that reads and processes markdown files
- `posts/markdown/` - Directory containing {$postCount} markdown files with YAML frontmatter
- `images/` - Directory containing post feature images
- `README.md` - This file

## Installation Instructions

### Step 1: Copy Files

1. Copy the migration file to your project's `database/migrations/` directory:
   ```bash
   cp {$timestamp}_populate_exported_posts.php /path/to/your/project/database/migrations/
   ```

2. Copy the posts directory (with markdown files) to your project's database directory:
   ```bash
   cp -r posts /path/to/your/project/database/
   ```

3. Copy the images to your project's public directory:
   ```bash
   cp -r images /path/to/your/project/public/
   ```

### Step 2: Run Migration

Run the migration to import the posts:

```bash
php artisan migrate
```

The migration will:
- Scan `database/posts/markdown/` for all .md files
- Parse YAML frontmatter from each file for post metadata and author data
- Read markdown content from each file for post body
- Create or update users/authors from author data in frontmatter (name, email, slug, bio, links, is_author)
- Generate random secure passwords for new users
- Attach user avatar images from `public/images/` directory
- Create/link categories and tags (creates new ones if they don't exist)
- Attach post feature images from `public/images/` directory
- Skip any posts that already exist (based on slug)
- Show detailed progress with console output
- Log all operations to Laravel logs for troubleshooting
- Track and report imported, skipped, and error counts

### Step 3: Verify

Check your posts in Filament:

```bash
php artisan serve
```

Navigate to your Filament admin panel and verify the posts were imported correctly.

### Step 4: Review Logs (Recommended)

Check Laravel logs for any warnings or errors during import:

```bash
tail -f storage/logs/laravel.log
```

The migration logs:
- Frontmatter parsing results
- User matching results
- Category and tag operations
- Image import warnings
- Any errors that occurred during import

### Step 5: Cleanup (Optional)

After successful import, you can optionally remove the markdown files if you no longer need them:

```bash
rm -rf database/posts/markdown/
```

**Note:** Images in `public/images/` should remain as they are actively used by your posts.

## Features

### Production-Ready Migration
- **No embedded data** - All post data lives in markdown files with frontmatter
- **Flexible editing** - Edit posts directly in .md files before importing
- **Comprehensive error handling** - Try/catch blocks around all operations
- **Detailed logging** - All operations logged to Laravel logs
- **Progress tracking** - Real-time console output with import statistics
- **Reversible** - Includes `down()` method to rollback the import
- **Smart user matching** - Matches by email with intelligent fallbacks
- **Idempotent** - Safe to run multiple times (skips duplicates)

### Data Integrity
- Post metadata and content stored in separate markdown files for easy editing
- Posts are matched by slug to prevent duplicates
- All post metadata preserved (status, publish dates, etc.)
- Categories and tags automatically created if missing
- Images stored in public directory for direct web access
- Images processed through Spatie Media Library with responsive images
- Uses your configured media disk from `config/sabhero-articles.php`

### Markdown File Format
Each post is exported as a markdown file with YAML frontmatter:

```markdown
---
export_order: 1
title: "Your Post Title"
slug: "your-post-slug"
sub_title: "Optional subtitle"
status: "published"
published_at: "2024-01-01 12:00:00"
user_email: "author@example.com"
categories:
  - Category One
  - Category Two
tags:
  - Tag One
  - Tag Two
post_feature_image: "image-filename.jpg"
post_feature_image_alt_text: "Image description"
---

Your markdown content here...
```

**Note:** User/author information is hardcoded in the migration file itself, not stored in the frontmatter. The migration creates or updates users with all their data (name, email, slug, bio, links, avatar) and generates random passwords for security.

**Note:** The `export_order` field determines import order. Posts are imported in **reverse order** (highest export_order first, lowest last), so the newest exported posts are imported first.

### Import Summary
After migration completes, you'll see a summary like:
```
════════════════════════════════════════════════════════════════
  Import Complete
────────────────────────────────────────────────────────────────
  ✓ Imported: 45
  ℹ Skipped:  3
  ✗ Errors:   0
════════════════════════════════════════════════════════════════
```

## Rollback

To reverse the migration and remove all imported posts:

```bash
php artisan migrate:rollback
```

**WARNING:** This will permanently delete all posts imported by this migration, along with their media files. The migration reads the markdown files to determine which posts to delete.

## Troubleshooting

### Markdown files not found
- Ensure markdown files were copied to `database/posts/markdown/`
- Check that markdown filenames end with `.md`
- Verify file permissions on the markdown directory (should be readable)
- Check console output during migration for file reading errors
- Review Laravel logs for "No markdown files found" errors

### Frontmatter parsing errors
- Ensure frontmatter is enclosed in `---` markers
- Verify YAML syntax is valid (use a YAML validator)
- Check that `title` and `slug` fields are present
- Ensure arrays use proper YAML format with `-` for items
- Review Laravel logs for "Failed to parse markdown file" errors

### Images not appearing
- Ensure images were copied to `public/images/`
- Check file permissions on the images directory (should be readable by web server)
- Verify images are web-accessible at `http://yoursite.com/images/filename.jpg`
- Verify your media disk configuration in `config/sabhero-articles.php`
- Check console output during migration for image warnings
- Review Laravel logs for detailed error messages

### Posts not importing
- Check that you've run `php artisan migrate` for the SabHero Articles package first
- Ensure the posts, categories, and tags tables exist
- Ensure markdown files are in `database/posts/markdown/`
- Check Laravel logs (`storage/logs/laravel.log`) for detailed errors
- Ensure at least one user exists in the database
- Verify database connection and permissions

### User assignment issues
- Users are created or updated automatically by the migration with all their data
- User data is hardcoded in the migration file (not in frontmatter)
- Random passwords are generated for new users for security
- User avatars are attached from the `public/images/` directory
- Posts are linked to users by email address
- If user email is not found, the first available user is used as fallback
- Check Laravel logs for "User not found by email" warnings
- You can manually update post authors or user data after import if needed

### Status and scheduling
- Post status values are preserved (published, draft, scheduled)
- Published dates and scheduled dates are maintained
- If importing to a different timezone, dates are preserved as-is
- Review imported posts in Filament to verify dates are correct

### Categories and tags not linking
- Categories and tags are created/updated automatically using `updateOrInsert`
- Duplicate categories/tags are prevented by matching on `name`
- Verify the pivot tables (`category_post`, `post_tag`) exist
- Check database table prefixes match your configuration
- Ensure pivot table insertions aren't failing (check logs for constraint errors)

## Technical Details

### Migration Structure
The migration uses a structured approach:

**Phase 1: User Creation**
1. **Create/update users** - All user data hardcoded in migration with random passwords
2. **Attach avatars** - User avatar images attached from public/images/

**Phase 2: Post Import (per post)**
1. **Parse markdown** - Read and parse .md file with YAML frontmatter
2. **Duplicate detection** - Skip posts that already exist
3. **User matching** - Find user by email from frontmatter
4. **Post creation** - Insert post with all metadata and content
5. **Category attachment** - Create/link categories
6. **Tag attachment** - Create/link tags
7. **Image processing** - Attach media files from public/images/

### Error Handling
All operations are wrapped in try/catch blocks with:
- Detailed error logging to Laravel logs
- Console output for immediate visibility
- Graceful degradation (continues on errors)
- Final summary with error counts

### Logging
The migration logs to Laravel's standard log file:
- **Info**: Successful operations (created categories, completed import)
- **Warning**: Non-critical issues (user not found, frontmatter errors, image missing)
- **Error**: Critical failures (no users available, parsing failed, import failed)

### Dependencies
This migration requires:
- `symfony/yaml` package for YAML parsing
- Spatie Media Library for image handling
- SabHero Articles package installed and migrated

## Generated

Exported on: " . now()->format('Y-m-d H:i:s') . "
From: " . request()->getHost() . "
MD;
    }

    /**
     * Create ZIP file from directory
     * Uses explicit file listing to avoid symlink resolution issues (Laravel Forge)
     */
    protected function createZip(string $zipFilePath, string $sourceDir): void
    {
        $zip = new \ZipArchive;

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP file');
        }

        // Add migration file at root (find the .php file)
        $migrationFiles = glob($sourceDir.'/*.php');
        foreach ($migrationFiles as $migrationFile) {
            if (is_file($migrationFile)) {
                $zip->addFile($migrationFile, basename($migrationFile));
            }
        }

        // Add README.md at root
        $readmeFile = $sourceDir.'/README.md';
        if (file_exists($readmeFile)) {
            $zip->addFile($readmeFile, 'README.md');
        }

        // Add markdown files in posts/markdown/
        $markdownFiles = glob($sourceDir.'/posts/markdown/*.md');
        foreach ($markdownFiles as $markdownFile) {
            if (is_file($markdownFile)) {
                $zip->addFile($markdownFile, 'posts/markdown/'.basename($markdownFile));
            }
        }

        // Add images in images/
        $imageFiles = glob($sourceDir.'/images/*');
        foreach ($imageFiles as $imageFile) {
            if (is_file($imageFile)) {
                $zip->addFile($imageFile, 'images/'.basename($imageFile));
            }
        }

        $zip->close();
    }
}
