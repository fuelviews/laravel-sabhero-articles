# Changelog

All notable changes to `laravel-sabhero-article` will be documented in this file.

## v0.0.15 - 2025-07-31

### What's Changed

* Add a new seeder for the Page model to populate initial page data and update the media conversion name for feature images to ensure consistency in the media library. This change enhances the database seeding process and improves the handling of feature images associated with pages. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-article/pull/25

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-article/compare/v0.0.14...v0.0.15

## v0.0.14 - 2025-07-31

### What's Changed

* Add 'feature_image' to the fillable attributes in the Page model and update the unique validation to use the class name dynamically while ensuring the slug is formatted to lowercase. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-article/pull/24

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-article/compare/v0.0.13...v0.0.14

## v0.0.13 - 2025-07-27

### What's Changed

* Update article config prefixes to plural, add 'Route' labels to slug fields for clarity, enhance Table of Contents styling for better dark mode support, and adjust service provider to register FeedServiceProvider after views are loaded. by @thejmitchener in https://github.com/fuelviews/laravel-sabhero-article/pull/23

**Full Changelog**: https://github.com/fuelviews/laravel-sabhero-article/compare/v0.0.12...v0.0.13

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
