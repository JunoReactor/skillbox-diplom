<?php

namespace App\Service;

use App\Repository\ArticleRepository;


class HomepageProvider
{
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getHomepageData()
    {
        $articles = $this->articleRepository->findLatestPublished();

        return [
            'articles' => $articles,
        ];
    }
}


