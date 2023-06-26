<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Entity\User;
use App\Homework\ArticleContentProviderInterface;
use App\Homework\CommentContentProvider;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private static $articleTitles = [
        'Что делать, если надо верстать?',
        'Facebook ест твои данные',
        'Когда пролил кофе на клавиатуру',
    ];

    private static $articleAuthors = [
        'Флекс Абсолютович',
        'Вью j эс',
        'Фронтенд Фулстэков',
        'Хэтээмэль Цеэсесович',
    ];

    private static $articleImages = [
        'article-1.jpeg',
        'article-2.jpeg',
        'article-3.jpg',
    ];
    
    
    private static $words = ['Кофе', 'Клавиатура', 'Пролить', 'Пить', 'Рукопоп'];
    
    
    /**
     * @var ArticleContentProviderInterface
     */
    private ArticleContentProviderInterface $articleContentProvider;
    /**
     * @var CommentContentProvider
     */
    private $commentContentProvider;

    public function __construct(ArticleContentProviderInterface $articleContentProvider, CommentContentProvider $commentContentProvider)
    {
        $this->articleContentProvider = $articleContentProvider;
        $this->commentContentProvider = $commentContentProvider;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Article::class, 30, function (Article $article) use ($manager) {
            $article
                ->setTitle($this->faker->randomElement(self::$articleTitles))
                ->setBody($this->getArticleContent())
                ->setDescription($this->faker->realText(100))
            ;

            if ($this->faker->boolean(60)) {
                $article->setPublishedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            }

            $article
                ->setAuthor($this->getRandomReference(User::class))
                ->setVoteCount($this->faker->numberBetween(-10, 10))
                ->setImageFilename($this->faker->randomElement(self::$articleImages))
                ->setKeywords($this->faker->realText(50))
            ;

            for ($i = 0; $i < $this->faker->numberBetween(2, 10); $i++) {
                $this->addComment($article, $manager);
            }

            /** @var Tag $tags */
            $tags = [];
            for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++) {
                $tags[] = $this->getRandomReference(Tag::class);
            }

            foreach ($tags as $tag) {
                $article->addTag($tag);
            }

        });
    }

    public function getDependencies()
    {
        return [
            TagFixtures::class,
            UserFixtures::class,
        ];
    }

    private function addComment($article, $manager)
    {
        $word = null;
        $wordsCount = 0;

        if ($this->faker->boolean(70)) {
            $word = $this->faker->randomElement(self::$words);
            $wordsCount = $this->faker->numberBetween(1, 5);
        }

        $comment = (new Comment())
            ->setAuthorName($this->faker->randomElement(self::$articleAuthors))
            ->setContent($this->commentContentProvider->get($word, $wordsCount))
            ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 day'))
            ->setArticle($article)
        ;

        if ($this->faker->boolean) {
            $comment->setDeletedAt($this->faker->dateTimeThisMonth);
        }

        $manager->persist($comment);
    }

    private function getArticleContent()
    {
        $word = null;
        $wordsCount = 0;
        $paragraphs = $this->faker->numberBetween(2, 10);

        if ($this->faker->boolean(70)) {
            $word = $this->faker->randomElement(self::$words);
            $wordsCount = $this->faker->numberBetween(2, $paragraphs * 2);
        }

        return $this->articleContentProvider->get($paragraphs, $word, $wordsCount);
    }
}
