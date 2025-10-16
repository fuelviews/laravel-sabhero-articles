<?php

return [
    'feeds' => [
        'articles' => [
            /*
             * Here you can specify which class and method will return
             * the items that should appear in the feed. For example:
             * 'App\Models\Post@getFeedItems'
             *
             * You can also pass an argument to that method:
             * ['App\Models\Post@getFeedItems', 'argument']
             */
            'items' => 'Fuelviews\SabHeroArticles\Models\Post@getFeedItems',

            /*
             * The feed will be available on this url.
             */
            'url' => config('sabhero-articles.route.prefix').'/rss',

            'title' => config('app.name').' Latest Article Posts',
            'description' => 'The latest article posts from '.config('app.url'),
            'language' => 'en-US',

            /*
             * The image to display for the feed.  For Atom feeds, this is displayed as
             * a banner/logo; for RSS and JSON feeds, it's displayed as an icon.
             * An empty value omits the image attribute from the feed.
             */
            'image' => '',

            /*
             * The format of the feed links: 'atom' or 'rss'.
             */
            'format' => 'rss',

            /*
             * The view that will render the feed.
             */
            'view' => 'sabhero-articles::feed.rss',

            /*
             * The type of feed to use: 'atom' or 'rss'
             */
            'type' => 'rss',

            /*
             * The content type for the feed response.  Set to an empty string to automatically
             * determine the correct value.
             */
            'contentType' => '',
        ],
    ],
];
