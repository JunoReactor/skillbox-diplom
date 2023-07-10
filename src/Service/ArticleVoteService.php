<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\ArticleVoteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleVoteController extends AbstractController
{
    /**
     * @Route("/articles/{slug}/vote/{type<up|down>}", methods={"POST"}, name="app_article_vote")
     */
    public function like(Article $article, $type, EntityManagerInterface $em, ArticleVoteService $articleVoteService)
    {
        $votes = $articleVoteService->vote($article, $type);

        return $this->json(['votes' => $votes]);
    }
}
