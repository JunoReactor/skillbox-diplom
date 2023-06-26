<?php

namespace App\Service;


use Demontpx\ParsedownBundle\Parsedown;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class MarkdownParser
{
    /**
     * @var Parsedown
     */
    private $parsedown;
    /**
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(Parsedown $parsedown, AdapterInterface $cache)
    {
        $this->parsedown = $parsedown;
        $this->cache = $cache;
    }

    public function parse(string $source): string 
    {
        return $this->cache->get(
            'markdown_'.md5($source),
            function () use ($source) {
                return $this->parsedown->text($source);
            }
        );
    }
}
