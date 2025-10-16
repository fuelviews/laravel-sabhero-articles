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
                'route' => 'title', // Laravel route name for this page
                'description' => 'Desc here',
                'page_feature_image' => null,
            ],
        ];

        foreach ($pages as $index => $pageData) {
            // Store the actual image path for media library
            $actualImagePath = $pageData['page_feature_image'] ?? null;

            // Remove page_feature_image from data array - it's not a database column
            // The actual image will be stored in the media library
            unset($pageData['page_feature_image']);

            // Check if a page with this route OR title already exists
            $existingPage = $pageModel::where('route', $pageData['route'])
                ->orWhere('title', $pageData['title'])
                ->first();

            $page = null;
            if ($existingPage) {
                // Update existing page (prioritize finding by route)
                $pageByRoute = $pageModel::where('route', $pageData['route'])->first();
                if ($pageByRoute) {
                    $pageByRoute->update($pageData);
                    $page = $pageByRoute;
                } else {
                    // If found by title, update that one
                    $existingPage->update($pageData);
                    $page = $existingPage;
                }
            } else {
                // Create new page only if neither route nor title exist
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
                        $mediaDisk = config('sabhero-articles.media.disk');

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
