<?php
namespace App\Service;

use App\Entity\Article;
use App\Provider\ArticleContentProvider;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class DashboardСreateArticle
{
    private EntityManagerInterface $em;
    private ArticleContentProvider $articleContentProvider;
    /**
     * DashboardСreateArticle constructor.
     *
     * @param EntityManagerInterface $em
     * @param ArticleContentProvider $articleContentProvider
     */
    public function __construct(EntityManagerInterface $em, ArticleContentProvider $articleContentProvider)
    {
        $this->em = $em;
        $this->articleContentProvider = $articleContentProvider;
    }
    /**
     * Create new article
     *
     * @param Request $request
     * @return string|null
     */
    public function createArticle(Request $request)
    {
        if (count($request->request) == 11) {
            $faker = Factory::create();

            $articleSizeFrom = $request->request->get('articleSizeFrom');
            $articleSizeTo = $request->request->get('articleSizeTo');
            $word1Field = $request->request->get('word1Field');
            $word1Field = $request->request->get('word1Field');

            $text = $faker->realText(
                $faker->numberBetween(
                    intval($request->request->get('articleSizeFrom')),
                    intval($request->request->get('articleSizeTo'))
                )
            );

            $article = new Article();
            $article->setTitle($request->request->get('articleTitle'));
            $article->setAuthor($this->getUser());
            $article->setKeywords(
                $request->request->get('article0Word') . ',' .
                $request->request->get('article1Word') . ',' .
                $request->request->get('article2Word')
            );

            $articleContent = $this->articleContentProvider->get(
                $text,
                $request->request->get('word1Field'),
                $request->request->get('word1CountField')
            );

            $articleContent = $this->articleContentProvider->get(
                $articleContent,
                $request->request->get('word2Field'),
                $request->request->get('word2CountField')
            );

            $uploadImg = $this->uploadFile($request->files->get('uploadImg'));
            $article->setImageFilename($uploadImg);
            $article->setDescription($request->request->get('fieldTheme'));

            $article->setBody($articleContent);
            $this->em->persist($article);
            $this->em->flush();

            return 'Статья добавлена';
        }

        return null;
    }
    /**
     * Upload file
     *
     * @param File|null $file
     * @return bool|string
     */
    public function uploadFile(?File $file)
    {
        if ($file === null) {
            // die('file null');
            return false;
        }

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move(
            dirname(dirname(__DIR__)) . '/public/uploads/i/',
            $fileName
        );

        return '/public/uploads/i/' . $fileName;
    }
}
