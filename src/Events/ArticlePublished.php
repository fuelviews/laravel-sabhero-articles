<?php

namespace Fuelviews\SabHeroArticle\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticlePublished
{
    use Dispatchable;
    use SerializesModels;

    public mixed $post;

    public function __construct($post)
    {
        $this->post = $post;
    }
}
