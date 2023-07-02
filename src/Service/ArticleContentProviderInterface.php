<?php

namespace App\Service;


interface ArticleContentProviderInterface
{
    public function get(int $paragraphs, string $word = null, int $wordsCount = 0): string;
}
