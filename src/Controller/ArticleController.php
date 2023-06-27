<?php

namespace App\Controller;


use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Service\ArticleContentProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(ArticleRepository $articleRepository, CommentRepository $commentRepository)
    {
        $articles = $articleRepository->findLatestPublished();
        $comments = $commentRepository->findThreeLatest();
        
        return $this->render('diplom/homepage.html.twig', [
            'articles' => $articles,
            'comments' => $comments,
        ]);
    }


    /**
     * @Route("/articles/article_content", name="app_article_content")
     */
    public function articleContent(Request $request, ArticleContentProviderInterface $articleContentProvider)
    {
        $paragraphs = (int)$request->get('paragraphs');
        $word       = $request->get('word');
        $wordsCount = (int)$request->get('wordsCount');

        $articleContent = $articleContentProvider->get($paragraphs, $word, $wordsCount);

        return $this->render('article/content.html.twig', [
            'articleContent' => $articleContent,
        ]);
    }
    
    /**
     * @Route("/articles/{slug}", name="app_article")
     */
    public function show(Article $article)
    {
        return $this->render('diplom/article/show.html.twig', [
            'article' => $article,
        ]);
    }


    /**
     * @Route("/try", name="app_article_try")
     */
    public function try()
    {
        return $this->render('diplom/try.html.twig');
    }
}
