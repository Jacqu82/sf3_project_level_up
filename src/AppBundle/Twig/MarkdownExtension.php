<?php

namespace AppBundle\Twig;

use AppBundle\Service\MarkdownTransformer;

class MarkdownExtension extends \Twig_Extension
{
    private $transformer;

    public function __construct(MarkdownTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function getName()
    {
        return 'app_markdown';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('markdownify', [$this, 'parseMarkdown'], ['is_safe' => ['html']])
        ];
    }

    public function parseMarkdown(string $str): string
    {
        return $this->transformer->parse($str);
    }
}
