<?php

namespace Fuelviews\SabHeroArticles\Renderers;

use InvalidArgumentException;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class TableOfContentsRenderer implements NodeRendererInterface
{
    public function render($node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! ($node instanceof TableOfContents)) {
            throw new InvalidArgumentException('Incompatible node type: '.get_class($node));
        }

        // Toggle button with expand/collapse functionality
        $toggleButton = new HtmlElement(
            'button',
            [
                'id' => 'toc-toggle',
                'class' => 'flex w-full items-center justify-between text-xl font-semibold mb-4 text-gray-900 dark:text-white hover:text-gray-700 dark:hover:text-gray-300 transition-colors',
                'aria-expanded' => 'false',
                'aria-controls' => 'toc-content',
            ],
            [
                new HtmlElement('span', [], 'Table of Contents'),
                new HtmlElement('svg', [
                    'class' => 'w-5 h-5 transition-transform duration-200',
                    'fill' => 'none',
                    'stroke' => 'currentColor',
                    'viewBox' => '0 0 24 24',
                ], '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>'),
            ]
        );

        // Render TOC content and modify <a> tags
        $tocHtml = $childRenderer->renderNodes($node->children());

        // Modify all <a> tags by injecting a custom class
        $tocHtml = preg_replace('/<a /', '<a class="underline underline-offset-[0.35rem] hover:opacity-75 hover:underline transition-colors text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white" ', $tocHtml);

        $toc = new HtmlElement(
            'ol',
            [
                'id' => 'toc-content',
                'class' => 'hidden pl-6 list-disc columns-1 md:columns-2 gap-6 not-prose text-gray-600 dark:text-gray-400 transition-all duration-300',
            ],
            $tocHtml
        );

        $tocContainer = new HtmlElement(
            'div',
            ['class' => 'rounded-2xl bg-gray-100 dark:bg-gray-800 p-4 mb-6 toc bg-gray-50 border border-gray-200'],
            [$toggleButton, $toc]
        );

        // JavaScript for toggle functionality with state persistence
        $javascript = '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.getElementById("toc-toggle");
            const content = document.getElementById("toc-content");
            const svg = toggle.querySelector("svg");
            const storageKey = "toc-expanded";

            if (toggle && content) {
                // Restore saved state or default to collapsed
                const savedState = localStorage.getItem(storageKey);
                const isExpanded = savedState === "true";

                // Apply saved state
                toggle.setAttribute("aria-expanded", isExpanded);
                if (isExpanded) {
                    content.classList.remove("hidden");
                    svg.style.transform = "rotate(180deg)";
                } else {
                    content.classList.add("hidden");
                    svg.style.transform = "rotate(0deg)";
                }

                // Toggle functionality
                toggle.addEventListener("click", function() {
                    const currentExpanded = toggle.getAttribute("aria-expanded") === "true";
                    const newExpanded = !currentExpanded;

                    // Update UI
                    toggle.setAttribute("aria-expanded", newExpanded);
                    content.classList.toggle("hidden");
                    svg.style.transform = newExpanded ? "rotate(180deg)" : "rotate(0deg)";

                    // Save state
                    localStorage.setItem(storageKey, newExpanded);
                });
            }
        });
        </script>';

        return (string) $tocContainer . $javascript;
    }
}
