<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Fuelviews\SabHeroArticles\Models\Page;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Note: This seeder uses the media disk configured in config/media-library.php
     * Default is 'media' disk, but can be overridden with MEDIA_DISK env variable
     */
    public function run(): void
    {
        // Check if a Page model exists from any package
        $pageModel = null;

        if (class_exists(Page::class)) {
            $pageModel = Page::class;
        }

        if (!$pageModel) {
            $this->command->warn('No Page model found. Skipping page seeder.');
            return;
        }

        $pages = [
            // Main Pages
            [
                'title' => 'Title',
                'slug' => 'title', // actually needs the route name, not the slug
                'description' => 'Desc here',
                'feature_image' => null,
            ],
        ];

        foreach ($pages as $index => $pageData) {
            // Store the actual image path for media library
            $actualImagePath = $pageData['feature_image'] ?? null;

            // Since feature_image is nullable in the database, we can set it to null
            // The actual image will be stored in the media library
            $pageData['feature_image'] = null;

            // Check if a page with this slug OR title already exists
            $existingPage = $pageModel::where('slug', $pageData['slug'])
                ->orWhere('title', $pageData['title'])
                ->first();

            $page = null;
            if ($existingPage) {
                // Update existing page (prioritize finding by slug)
                $pageBySlug = $pageModel::where('slug', $pageData['slug'])->first();
                if ($pageBySlug) {
                    $pageBySlug->update($pageData);
                    $page = $pageBySlug;
                } else {
                    // If found by title, update that one
                    $existingPage->update($pageData);
                    $page = $existingPage;
                }
            } else {
                // Create new page only if neither slug nor title exist
                $page = $pageModel::create($pageData);
            }

            // Add feature image to media library if provided and model supports it
            if ($actualImagePath && $page && method_exists($page, 'hasMedia') && method_exists($page, 'addMedia')) {
                try {
                    // Build the full path to the image
                    $imagePath = public_path($actualImagePath);

                    if (file_exists($imagePath)) {
                        // Clear existing media first, then add new one
                        $page->clearMediaCollection('page_feature_image');

                        // Get the configured media disk from config
                        $mediaDisk = config('sabhero-articles.media.disk', 'public');

                        $page->addMedia($imagePath)
                            ->preservingOriginal()
                            ->withResponsiveImages()
                            ->toMediaCollection('page_feature_image', $mediaDisk);
                    } else {
                        $this->command->warn("Image file not found for page '{$page->title}': {$imagePath}");
                    }
                } catch (\Exception $e) {
                    $this->command->warn("Could not add feature image for page '{$page->title}': " . $e->getMessage());
                }
            }
        }
    }
}
