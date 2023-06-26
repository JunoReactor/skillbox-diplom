<?php

namespace App\Controller\Api;

use App\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/api/v1/article/{id}", name="api_article")
     * @IsGranted("API_MANAGE", subject="article")
     */
    public function index(Article $article)
    {
        return $this->json($article, 200, [], ['groups' => ['main']]);
    }
}
