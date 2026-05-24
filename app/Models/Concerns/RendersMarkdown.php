<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

trait RendersMarkdown
{
    /**
     * Render user-typed markdown to a tightly-scoped HTML subset.
     *
     * Two layers of defense:
     *   1. CommonMark with `html_input: escape` + `allow_unsafe_links: false`
     *      — raw HTML in the source is escaped, javascript:/data: URLs blocked.
     *   2. Symfony HtmlSanitizer with an explicit allowlist of tags + attrs.
     *      Anything outside the list is stripped from the rendered output,
     *      so even a CommonMark regression couldn't smuggle a script tag.
     *
     * Allowed tags: paragraphs, emphasis (bold/italic), inline code, links,
     * lists, line breaks, blockquotes. No headings, code blocks, tables,
     * images, or raw HTML.
     */
    protected function renderMarkdown(?string $source): string
    {
        if ($source === null || trim($source) === '') {
            return '';
        }

        $html = Str::markdown($source, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        return $this->markdownSanitizer()->sanitize($html);
    }

    private function markdownSanitizer(): HtmlSanitizer
    {
        static $sanitizer = null;

        if ($sanitizer === null) {
            $config = (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('code')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('br')
                ->allowElement('blockquote')
                ->allowElement('a', ['href'])
                ->allowLinkSchemes(['http', 'https', 'mailto'])
                ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
                ->forceAttribute('a', 'target', '_blank');

            $sanitizer = new HtmlSanitizer($config);
        }

        return $sanitizer;
    }
}
