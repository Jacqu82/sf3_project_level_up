<?php

namespace AppBundle\Service;

use Doctrine\Common\Cache\Cache;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{
    private $markdown;

    private $cache;

    public function __construct(MarkdownParserInterface $markdown, Cache $cache)
    {
        $this->markdown = $markdown;
        $this->cache = $cache;
    }

    public function parse(string $str): string
    {
        $cache = $this->cache;
        $key = md5($str);
        if ($cache->contains($key)) {
            return $cache->fetch($key);
        }

        sleep(1);
        $str = $this->markdown->transformMarkdown($str);
        $cache->save($key, $str);

        return $str;
    }
}
