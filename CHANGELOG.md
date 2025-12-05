# Changelog

All notable changes to `laravel-sabhero-articles` will be documented in this file.

## v2.0.4 - 2025-12-05

### What's Changed

* Add feature_image column to pages table and enhance user password handling by adding password confirmation and hashing; improve slug generation and author status assignment in HasArticle trait. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/49

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v2.0.3...v2.0.4

## v2.0.3 - 2025-11-01

### What's Changed

* Update breadcrumb label to use plural and ucfirst formatting for route prefix to improve display consistency. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/47

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v2.0.2...v2.0.3

## v2.0.2 - 2025-10-22

### What's Changed

* Refactor ZIP creation to use explicit file listing instead of recursive directory iteration to avoid symlink resolution issues on Laravel Forge. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/44

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v2.0.1...v2.0.2

## v2.0.1 - 2025-10-20

### What's Changed

* Change post import order to reverse (newest first) to prioritize importing the latest posts before older ones. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/43

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v2.0.0...v2.0.1

## v2.0.0 - 2025-10-16

### What's Changed

* Bump actions/checkout from 4 to 5 by @dependabot[bot] in https://github.com/fuelviews/laravel-sabhero-articles/pull/35
* Refactor and unify resources, add migration import/export, add upgrade command, update README, etc. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/42

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v1.0.3...v2.0.0

## v1.0.3 - 2025-10-14

### What's Changed

* Add PostExportAction and PostImportAction classes to handle exportingand importing posts as ZIP files with CSV and images; refactor PostResource to delegate export and import logic to these new actions for cleaner and more maintainable code. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/39

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v1.0.2...v1.0.3

## v1.0.2 - 2025-09-29

### What's Changed

* Simplify media disk configuration by hardcoding 'public' and update PageTableSeeder with clearer page data and improved seeding notes, removing redundant info messages. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/38

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v1.0.1...v1.0.2

## v1.0.1 - 2025-09-22

### What's Changed

* Bump platisd/openai-pr-description from 1.4.0 to 1.5.0 by @dependabot[bot] in https://github.com/fuelviews/laravel-sabhero-articles/pull/34
* Bump anothrNick/github-tag-action from 1.73.0 to 1.75.0 by @dependabot[bot] in https://github.com/fuelviews/laravel-sabhero-articles/pull/36
* Add configurable media storage disk support across the package, update README and config for customization, and modify media handling in models, resources, and seeders to use the configured disk. Adjust page title length and nullable description, and improve feature image seeding with local file checks and media library integration. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/37

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v1.0.0...v1.0.1

## v1.0.0 - 2025-08-28

### What's Changed

* Rename package to be plural. #major by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/33

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v0.0.18...v1.0.0

## v0.0.18 - 2025-08-28

### What's Changed

* Integrate ralphjsmit glide by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/28

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v0.0.17...v0.0.18

## v0.0.17 - 2025-08-28

### What's Changed

* Improve article views layout and styling, enhance breadcrumb link truncation, adjust app layout padding and margins, and add a collapsible, state-persistent toggle button to the Table of Contents for better usability. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/27

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v0.0.16...v0.0.17

## v0.0.16 - 2025-08-07

### What's Changed

* Add pages table migration and seeder publishing; update README with seeder publishing and running instructions; remove unused package dependency. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/26

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v0.0.15...v0.0.16

## v0.0.15 - 2025-07-31

### What's Changed

* Add a new seeder for the Page model to populate initial page data and update the media conversion name for feature images to ensure consistency in the media library. This change enhances the database seeding process and improves the handling of feature images associated with pages. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/25

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v0.0.14...v0.0.15

## v0.0.14 - 2025-07-31

### What's Changed

* Add 'feature_image' to the fillable attributes in the Page model and update the unique validation to use the class name dynamically while ensuring the slug is formatted to lowercase. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/24

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v0.0.13...v0.0.14

## v0.0.13 - 2025-07-27

### What's Changed

* Update article config prefixes to plural, add 'Route' labels to slug fields for clarity, enhance Table of Contents styling for better dark mode support, and adjust service provider to register FeedServiceProvider after views are loaded. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-articles/pull/23

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-articles/compare/v0.0.12...v0.0.13

## v0.0.10 - 2025-06-17

### What's Changed

* Remove metros by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/17

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/compare/v0.0.9...v0.0.10

## v0.0.9 - 2025-06-02

### What's Changed

* Change the visibility of the  method from protected to public to allow access from outside the class. This modification enhances the usability of the  column in the application. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/16

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/compare/v0.0.8...v0.0.9

## v0.0.8 - 2025-05-10

### What's Changed

* Update GitHub Actions workflow to restrict permissions for pull requests and contents, and expand Laravel version support in the testing matrix. Enhance README for clarity and detail, and add a new Tailwind pagination view for improved UI. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/13

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.8

## v0.0.7 - 2025-05-07

### What's Changed

* Refactor author avatar handling by introducing dedicated methods for retrieving avatar URLs and srcsets, improving code readability and maintainability. This change standardizes how author avatars are displayed across various views. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/11

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.7

## v0.0.6 - 2025-04-29

### What's Changed

* Refactor breadcrumb and route names to use 'index' instead of 'all' for consistency and clarity in the sabhero-blog application. This change improves the semantic meaning of the routes and breadcrumbs. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/10

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.6

## v0.0.5 - 2025-04-26

### What's Changed

* Refactor image handling to utilize responsive images for avatars and feature images across various views, enhancing performance and user experience. Removed unnecessary comments and cleaned up code for better readability. (#8) by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/9

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.5

## v0.0.4 - 2025-04-01

### What's Changed

* Fix: Fix service provider bug by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/7

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.4

## v0.0.3 - 2025-03-31

### What's Changed

* Refactor: Remove portfolio (to it's own package) by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/5

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.3

## v0.0.2 - 2025-03-15

### What's Changed

* Update diglactic/laravel-breadcrumbs requirement from ^9.0 to ^10.0 by @dependabot in https://github.com/fuelviews/laravel-sabhero-blog/pull/4

### New Contributors

* @dependabot made their first contribution in https://github.com/fuelviews/laravel-sabhero-blog/pull/4

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.2

## v0.0.1 - 2025-03-04

### What's Changed

* Introduce migrations, resources, and features for blog module by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-blog/pull/2

### New Contributors

* @thejmitchener made their first contribution in https://github.com/fuelviews/laravel-sabhero-blog/pull/2

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-blog/commits/v0.0.1
