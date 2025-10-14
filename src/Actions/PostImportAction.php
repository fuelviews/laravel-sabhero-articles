<?php

namespace Fuelviews\SabHeroArticles\Actions;

use App\Models\User;
use Filament\Notifications\Notification;
use Fuelviews\SabHeroArticles\Models\Category;
use Fuelviews\SabHeroArticles\Models\Post;
use Fuelviews\SabHeroArticles\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

use function parse_url;

/**
 * Post Import Action
 *
 * Handles importing posts from ZIP format containing CSV and images.
 * Supports cloud storage by downloading to temp, then uploading to media library.
 */
class PostImportAction
{
    /**
     * Execute the import action
     *
     * @param  string  $zipFile  The uploaded ZIP file path on storage disk
     */
    public function execute(string $zipFile): void
    {
        $tempLocalZipPath = null;
        $extractPath = null;

        try {
            // Get Filament upload disk configuration
            $uploadDisk = config('filament.default_filesystem_disk', 'public');

            // Validate file exists
            if (! Storage::disk($uploadDisk)->exists($zipFile)) {
                $this->sendNotification('Import failed', 'Uploaded ZIP file not found.', 'danger');

                return;
            }

            // Download to local temp for processing
            [$tempLocalZipPath, $extractPath] = $this->downloadAndExtract($uploadDisk, $zipFile);

            // Find and validate CSV
            $csvFilePath = $this->findCsvFile($extractPath);

            if (! $csvFilePath) {
                // Delete only the original uploaded file from Filament disk
                if (Storage::disk($uploadDisk)->exists($zipFile)) {
                    Storage::disk($uploadDisk)->delete($zipFile);
                }

                $this->sendNotification('Import failed', 'No CSV file found in the extracted ZIP.', 'danger');

                return;
            }

            // Process CSV records
            $this->processRecords($csvFilePath, $extractPath);

            // Delete only the original uploaded file from Filament disk
            if (Storage::disk($uploadDisk)->exists($zipFile)) {
                Storage::disk($uploadDisk)->delete($zipFile);
            }

            // Success notification
            $this->sendNotification('Posts imported successfully!', null, 'success');

        } catch (\Exception $e) {
            $this->sendNotification('Import failed', 'An error occurred: '.$e->getMessage(), 'danger');
        }
    }

    /**
     * Download ZIP from storage and extract to local temp directory
     *
     * @return array{string, string} [$tempLocalZipPath, $extractPath]
     */
    protected function downloadAndExtract(string $uploadDisk, string $zipFile): array
    {
        $timestamp = time();
        $tempLocalZipPath = storage_path('app/sabhero-articles/temp/import_'.$timestamp.'.zip');
        $extractPath = storage_path('app/sabhero-articles/temp/import_extract_'.$timestamp);

        // Ensure temp directory exists
        if (! file_exists(dirname($tempLocalZipPath))) {
            mkdir(dirname($tempLocalZipPath), 0755, true);
        }

        // Download file to local temp storage
        $fileContents = Storage::disk($uploadDisk)->get($zipFile);
        file_put_contents($tempLocalZipPath, $fileContents);

        // Create extraction directory
        if (! file_exists($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        // Extract the ZIP file
        $zip = new ZipArchive;
        $openResult = $zip->open($tempLocalZipPath);

        if ($openResult === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            throw new RuntimeException("Failed to extract ZIP file (Error code: {$openResult}).");
        }

        return [$tempLocalZipPath, $extractPath];
    }

    /**
     * Find CSV file in extracted directory
     */
    protected function findCsvFile(string $extractPath): ?string
    {
        foreach (scandir($extractPath) as $file) {
            if (Str::endsWith($file, '.csv')) {
                return $extractPath.'/'.$file;
            }
        }

        return null;
    }

    /**
     * Process CSV records and import posts
     */
    protected function processRecords(string $csvFilePath, string $extractPath): void
    {
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $record) {
            $this->importPost($record, $extractPath);
        }
    }

    /**
     * Import a single post from CSV record
     */
    protected function importPost(array $record, string $extractPath): void
    {
        $post = Post::updateOrCreate(
            ['slug' => $record['Slug']],
            [
                'title' => $record['Title'] ?? '',
                'sub_title' => $record['Subtitle'] ?? '',
                'body' => $record['Content'] ?? '',
                'status' => $record['Status'] ?? '',
                'user_id' => optional(User::where('name', $record['Author'])->first())->id ?? '1',
                'feature_image_alt_text' => $record['Feature Image Alt Text'] ?? '',
                'published_at' => ! empty($record['Published At']) ? $record['Published At'] : null,
                'scheduled_for' => ! empty($record['Scheduled For']) ? $record['Scheduled For'] : null,
                'created_at' => ! empty($record['Created At']) ? $record['Created At'] : null,
                'updated_at' => ! empty($record['Updated At']) ? $record['Updated At'] : null,
            ]
        );

        // Import categories
        $this->importCategories($post, $record);

        // Import tags
        $this->importTags($post, $record);

        // Import images
        $this->importImages($post, $record, $extractPath);
    }

    /**
     * Import categories for a post
     */
    protected function importCategories(Post $post, array $record): void
    {
        if (! empty($record['Categories'])) {
            $categoryNames = explode(',', $record['Categories']);
            $categoryIds = [];

            foreach ($categoryNames as $catName) {
                $catName = trim($catName ?? '');
                if (empty($catName)) {
                    continue;
                }

                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($catName)],
                    ['name' => Str::lower($catName)]
                );

                $categoryIds[] = $category->id;
            }

            if (! empty($categoryIds)) {
                $post->categories()->sync($categoryIds, false);
            }
        }
    }

    /**
     * Import tags for a post
     */
    protected function importTags(Post $post, array $record): void
    {
        if (! empty($record['Tags'])) {
            $tagNames = explode(',', $record['Tags']);
            $tagIds = [];

            foreach ($tagNames as $tagName) {
                $tagName = trim($tagName ?? '');
                if (empty($tagName)) {
                    continue;
                }

                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => Str::lower($tagName)]
                );

                $tagIds[] = $tag->id;
            }

            if (! empty($tagIds)) {
                $post->tags()->sync($tagIds, false);
            }
        }
    }

    /**
     * Import images for a post
     */
    protected function importImages(Post $post, array $record, string $extractPath): void
    {
        $imageUrls = explode(', ', $record['Additional Media'] ?? '');

        foreach ($imageUrls as $imageUrl) {
            $imageName = basename(parse_url($imageUrl, PHP_URL_PATH));
            $imagePath = $extractPath.'/images/'.$imageName;

            if (file_exists($imagePath)) {
                $post->clearMediaCollection('post_feature_image');
                $post->addMedia($imagePath)
                    ->preservingOriginal() // Keep original file in temp directory
                    ->toMediaCollection('post_feature_image');
            }
        }
    }

    /**
     * Clean up all temporary files
     */
    protected function cleanup(?string $tempLocalZipPath, ?string $extractPath, string $uploadDisk, string $zipFile): void
    {
        // Delete temp ZIP file
        if ($tempLocalZipPath && file_exists($tempLocalZipPath)) {
            unlink($tempLocalZipPath);
        }

        // Recursively delete extract directory
        if ($extractPath && file_exists($extractPath)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($extractPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            rmdir($extractPath);
        }

        // Delete original uploaded file from configured disk
        if (Storage::disk($uploadDisk)->exists($zipFile)) {
            Storage::disk($uploadDisk)->delete($zipFile);
        }
    }

    /**
     * Send Filament notification
     */
    protected function sendNotification(string $title, ?string $body, string $type): void
    {
        $notification = Notification::make()->title($title);

        if ($body) {
            $notification->body($body);
        }

        match ($type) {
            'success' => $notification->success(),
            'danger' => $notification->danger(),
            default => $notification->info(),
        };

        $notification->send();
    }
}
