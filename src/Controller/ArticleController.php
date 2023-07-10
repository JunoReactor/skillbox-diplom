<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\ArticleContentProviderInterface;
use App\Service\HomepageProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     *
     * This method handles the homepage route.
     *
     * @param HomepageProvider $homepageProvider
     *
     * @return Response
     */
    public function homepage(HomepageProvider $homepageProvider)
    {
        $data = $homepageProvider->getHomepageData();

        return $this->render('diplom/homepage.html.twig', $data);
    }

    /**
     * @Route("/articles/article_content", name="app_article_content")
     *
     * This method handles the article content route.
     *
     * @param Request $request
     * @param ArticleContentProviderInterface $articleContentProvider
     *
     * @return Response
     */
    public function articleContent(Request $request, ArticleContentProviderInterface $articleContentProvider)
    {
        // логика получения контента статьи
    }

    /**
     * @Route("/articles/{slug}", name="app_article")
     *
     * This method handles the article page route.
     *
     * @param Article $article
     *
     * @return Response
     */
    public function show(Article $article)
    {
        return $this->render('diplom/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/try", name="app_article_try")
     *
     * This method handles the try page route.
     *
     * @return Response
     */
    public function try()
    {
        return $this->render('diplom/try.html.twig');
    }
}
