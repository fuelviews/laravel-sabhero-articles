<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Fuelviews\SabHeroArticles\Models\Page;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
            [
                'title' => 'Title one',
                'slug' => 'home',
                'description' => 'Description here',
                'feature_image' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&h=600&fit=crop',
            ],
        ];

        foreach ($pages as $pageData) {
            // Extract feature image URL if present
            $featureImageUrl = $pageData['feature_image'] ?? null;
            unset($pageData['feature_image']); // Remove from page data

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

            // Add feature image to media library if URL provided and model supports it
            if ($featureImageUrl && $page && method_exists($page, 'hasMedia') && method_exists($page, 'addMediaFromUrl')) {
                try {
                    // Clear existing media first, then add new one
                    $page->clearMediaCollection('page_feature_image');
                    $page->addMediaFromUrl($featureImageUrl)
                        ->toMediaCollection('page_feature_image');
                    $this->command->info("Added feature image for page: {$page->title}");
                } catch (\Exception $e) {
                    $this->command->warn("Could not add feature image for page '{$page->title}': " . $e->getMessage());
                }
            }
        }
    }
}
