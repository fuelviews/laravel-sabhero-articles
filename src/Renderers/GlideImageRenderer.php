<?php

namespace Fuelviews\SabHeroArticle\Renderers;

use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Node\StringContainerInterface;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use League\Glide\ServerFactory;
use League\Glide\Urls\UrlBuilderFactory;

final class GlideImageRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;
    
    private array $glideConfig;
    
    public function __construct(array $glideConfig = [])
    {
        $this->glideConfig = array_merge([
            'responsive' => true,
            'lazy_loading' => true,
            'srcset_widths' => [400, 800, 1200, 1600],
            'sizes' => '(max-width: 768px) 100vw, 50vw',
        ], $glideConfig);
    }

    /**
     * @param Image $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        Image::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');
        $originalUrl = $node->getUrl();

        $forbidUnsafeLinks = ! $this->config->get('allow_unsafe_links');
        if ($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($originalUrl)) {
            $attrs['src'] = '';
        } else {
            $attrs['src'] = $this->processImageUrl($originalUrl);
        }

        $attrs['alt'] = $this->getAltText($node);

        if (($title = $node->getTitle()) !== null) {
            $attrs['title'] = $title;
        }

        // Add responsive srcset if configured
        if ($this->glideConfig['responsive']) {
            $attrs['srcset'] = $this->generateSrcset($originalUrl);
            $attrs['sizes'] = $this->glideConfig['sizes'];
        }

        // Add lazy loading if configured
        if ($this->glideConfig['lazy_loading']) {
            $attrs['loading'] = 'lazy';
        }

        return new HtmlElement('img', $attrs, '', true);
    }

    /**
     * Process image URL through Glide
     */
    private function processImageUrl(string $url): string
    {
        // Skip external URLs
        if ($this->isExternalUrl($url)) {
            return $url;
        }

        // Normalize the image path for Glide (convert /storage/images/ to images/)
        $normalizedUrl = $this->normalizeImagePath($url);

        // Use glide()->src() to get the processed URL
        return glide()->src($normalizedUrl)->get('src');
    }

    /**
     * Generate responsive srcset attribute
     */
    private function generateSrcset(string $url): string
    {
        // Skip external URLs
        if ($this->isExternalUrl($url)) {
            return '';
        }

        // Normalize the URL once for all srcset generation
        $normalizedUrl = $this->normalizeImagePath($url);
        
        // Use Laravel Glide's built-in srcset generation
        $maxWidth = max($this->glideConfig['srcset_widths']);
        $attributes = glide()->src($normalizedUrl, $maxWidth, sizes: $this->glideConfig['sizes']);
        
        return $attributes->get('srcset');
    }

    /**
     * Check if URL is external
     */
    private function isExternalUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        return $host !== null && $host !== $appHost;
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getXmlTagName(Node $node): string
    {
        return 'image';
    }

    /**
     * @param Image $node
     *
     * @return array<string, scalar>
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getXmlAttributes(Node $node): array
    {
        Image::assertInstanceOf($node);

        return [
            'destination' => $node->getUrl(),
            'title' => $node->getTitle() ?? '',
        ];
    }

    private function getAltText(Image $node): string
    {
        $altText = '';

        foreach ((new NodeIterator($node)) as $n) {
            if ($n instanceof StringContainerInterface) {
                $altText .= $n->getLiteral();
            } elseif ($n instanceof Newline) {
                $altText .= "\n";
            }
        }

        return $altText;
    }
    
    /**
     * Normalize image path for Glide processing
     * Convert /storage/images/ to images/ since Glide expects relative paths
     */
    private function normalizeImagePath(string $url): string
    {
        // Remove the /storage/ prefix if present
        $path = ltrim($url, '/');
        
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8); // Remove 'storage/' prefix
        }
        
        return $path;
    }
}