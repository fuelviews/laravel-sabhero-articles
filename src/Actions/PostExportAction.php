<?php

namespace Fuelviews\SabHeroArticles\Actions;

use Filament\Notifications\Notification;
use Fuelviews\SabHeroArticles\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use RuntimeException;
use ZipArchive;

/**
 * Post Export Action
 *
 * Handles exporting posts to ZIP format with CSV and images.
 * Supports cloud storage (S3, etc.) by downloading files to temp storage.
 */
class PostExportAction
{
    /**
     * Execute the export action
     *
     * @param  Collection<int, Post>  $posts
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function execute(Collection $posts)
    {
        $tempWorkPath = null;
        $zipFilePath = null;

        try {
            // Create temp directories for export
            $timestamp = time();
            $tempWorkPath = storage_path('app/sabhero-articles/temp/export_zip_'.$timestamp);
            $tempMediaPath = $tempWorkPath.'/images';

            $this->createTempDirectory($tempMediaPath);

            // Generate descriptive filename and place ZIP outside work directory
            $exportFileName = $this->generateFilename($posts->count());
            $zipFilePath = storage_path('app/sabhero-articles/temp/'.$exportFileName);
            $csvFilePath = $tempWorkPath.'/posts.csv';

            // Create CSV with post data
            $this->createCsv($csvFilePath, $posts, $tempMediaPath);

            // Create ZIP file
            $this->createZip($zipFilePath, $csvFilePath, $tempMediaPath);

            // Keep working directory for debugging/review (don't clean up)
            // Keep ZIP file as well (don't delete after send)

            // Download ZIP without deleting it
            return response()->download($zipFilePath);

        } catch (\Exception $e) {
            // Keep temp files even on error for debugging

            Notification::make()
                ->title('Export failed')
                ->body('An error occurred: '.$e->getMessage())
                ->danger()
                ->send();

            return back();
        }
    }

    /**
     * Create temporary directory for export
     */
    protected function createTempDirectory(string $path): void
    {
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Generate descriptive export filename
     *
     * Format: domain_count_posts_export_on_date_at_time.zip
     */
    protected function generateFilename(int $postCount): string
    {
        $domain = str_replace(
            ['http://', 'https://', 'www.', '.', '-'],
            ['', '', '', '_', '_'],
            request()->getHost()
        );

        $estTime = now()->tz('America/New_York');
        $date = $estTime->format('Y_m_d');
        $time = strtolower($estTime->format('h_i_a'));

        return "{$domain}_{$postCount}_posts_export_on_{$date}_at_{$time}.zip";
    }

    /**
     * Create CSV file with post data and process media files
     */
    protected function createCsv(string $csvFilePath, Collection $posts, string $tempMediaPath): void
    {
        $csv = Writer::createFromPath($csvFilePath, 'w+');

        // Write CSV headers
        $csv->insertOne([
            'ID',
            'Title',
            'Subtitle',
            'Content',
            'Slug',
            'Status',
            'Categories',
            'Tags',
            'Feature Image Alt Text',
            'Additional Media',
            'Author',
            'Published At',
            'Scheduled For',
            'Created At',
            'Updated At',
        ]);

        // Write post data and copy media files
        $mediaDisk = config('sabhero-articles.media.disk', 'public');

        foreach ($posts as $post) {
            $mediaUrls = $this->copyMediaFilesToTemp($post, $tempMediaPath, $mediaDisk);

            $csv->insertOne([
                $post->id ?? '',
                $post->title ?? '',
                $post->sub_title ?? '',
                $post->body ?? '',
                $post->slug ?? '',
                $post->status->value ?? '',
                $post->categories->pluck('name')->implode(',') ?? '',
                $post->tags->pluck('name')->implode(',') ?? '',
                $post->feature_image_alt_text ?? '',
                implode(', ', $mediaUrls) ?? '',
                $post->user?->name ?? '',
                $post->published_at ? $post->published_at->format('Y-m-d H:i:s') : '',
                $post->scheduled_for ? $post->scheduled_for->format('Y-m-d H:i:s') : '',
                $post->created_at ? $post->created_at->format('Y-m-d H:i:s') : '',
                $post->updated_at ? $post->updated_at->format('Y-m-d H:i:s') : '',
            ]);
        }
    }

    /**
     * Copy media files to temp directory for ZIP inclusion
     *
     * @return array<int, string>
     */
    protected function copyMediaFilesToTemp(Post $post, string $tempMediaPath, string $mediaDisk): array
    {
        $mediaUrls = [];

        foreach ($post->getMedia('post_feature_image') as $media) {
            try {
                $mediaPath = $media->getPath();

                // If using cloud storage, download to temp
                if (! file_exists($mediaPath)) {
                    $diskPath = str_replace(Storage::disk($mediaDisk)->path(''), '', $mediaPath);

                    if (Storage::disk($mediaDisk)->exists($diskPath)) {
                        $tempFilePath = $tempMediaPath.'/'.$media->file_name;
                        $fileContents = Storage::disk($mediaDisk)->get($diskPath);
                        file_put_contents($tempFilePath, $fileContents);
                        $mediaPath = $tempFilePath;
                    }
                }

                // Copy local file to temp directory if not already there
                if (file_exists($mediaPath) && dirname($mediaPath) !== $tempMediaPath) {
                    $tempFilePath = $tempMediaPath.'/'.$media->file_name;
                    if (! file_exists($tempFilePath)) {
                        copy($mediaPath, $tempFilePath);
                    }
                }

                // Track media URL if file exists
                if (file_exists($tempMediaPath.'/'.$media->file_name)) {
                    $mediaUrls[] = $media->getUrl();
                }
            } catch (\Exception $e) {
                Log::warning('Failed to export media file: '.$e->getMessage());
            }
        }

        return $mediaUrls;
    }

    /**
     * Create ZIP file with CSV and images
     */
    protected function createZip(string $zipFilePath, string $csvFilePath, string $tempMediaPath): void
    {
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Failed to create ZIP file.');
        }

        // Add images to ZIP
        $imageFiles = glob($tempMediaPath.'/*');

        foreach ($imageFiles as $imageFile) {
            if (is_file($imageFile)) {
                $zip->addFile($imageFile, 'images/'.basename($imageFile));
            }
        }

        // Add CSV to ZIP
        $zip->addFile($csvFilePath, 'posts.csv');
        $zip->close();
    }
}
