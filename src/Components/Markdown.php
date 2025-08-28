<?php

namespace Fuelviews\SabHeroArticle\Components;

use Fuelviews\SabHeroArticle\Renderers\GlideImageRenderer;
use Fuelviews\SabHeroArticle\Renderers\TableOfContentsRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Illuminate\View\Component;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class Markdown extends Component
{
    public mixed $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function render(): View
    {
        $headingConfig = Config::get('sabhero-article.heading_permalink', []);
        $glideConfig = Config::get('sabhero-article.glide', []);

        $config = [
            'table_of_contents' => [
                'position' => 'top',
                'style' => 'ordered',
                'normalize' => 'flat',
                'placeholder' => null,
            ],
            'heading_permalink' => array_merge([
                'html_class' => 'scroll-mt-40',
                'id_prefix' => 'content',
                'symbol' => '',
                'aria_hidden' => true,
                'title' => 'Permalink',
                'min_heading_level' => 1,
                'max_heading_level' => 6,
            ], $headingConfig),
            'external_link' => [
                'html_class' => 'underline-offset-[0.35rem] hover:opacity-75 hover:underline',
            ],
            'table' => [
                'wrap' => [
                    'enabled' => false,
                    'tag' => 'div',
                    'attributes' => [],
                ],
            ],
            'embed' => [
                'adapter' => new OscaroteroEmbedAdapter,
                'allowed_domains' => ['youtube.com', 'twitter.com', 'github.com'],
                'fallback' => 'link',
            ],
            'footnote' => [
                'backref_class' => 'footnote-backref',
                'backref_symbol' => '↩',
                'container_add_hr' => true,
                'container_class' => 'footnotes',
                'ref_class' => 'footnote-ref',
                'ref_id_prefix' => 'fnref:',
                'footnote_class' => 'footnote',
                'footnote_id_prefix' => 'fn:',
            ],
        ];

        $environment = new Environment($config);

        $environment->addExtension(new CommonMarkCoreExtension);

        $environment->addExtension(new GithubFlavoredMarkdownExtension);

        $environment->addExtension(new TableOfContentsExtension);

        $environment->addRenderer(TableOfContents::class, new TableOfContentsRenderer);

        // Register custom Glide image renderer with Glide config
        $glideRenderer = new GlideImageRenderer($glideConfig);
        $environment->addRenderer(Image::class, $glideRenderer, 10);

        $environment->addExtension(new HeadingPermalinkExtension);

        $environment->addExtension(new AutolinkExtension);

        $environment->addExtension(new ExternalLinkExtension);

        $environment->addExtension(new TaskListExtension);

        $environment->addExtension(new EmbedExtension);

        $environment->addExtension(new FootnoteExtension);

        $environment->addExtension(new DescriptionListExtension);

        $environment->addExtension(new TableExtension);

        $converter = new MarkdownConverter($environment);

        $html = $converter->convert($this->content)->getContent();

        return view('sabhero-article::components.markdown', [
            'html' => $html,
        ]);
    }
}
