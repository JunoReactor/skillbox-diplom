<?php

namespace App\Homework;


use function count;

class ArticleProvider
{
    public function articles()
    {
        return [
            [
                'title' => 'Что делать, если надо верстать?',
                'image' => 'images/article-2.jpeg',
                'slug'  => 'what-to-do-if-need-fronted',
            ],
            [
                'title' => 'Facebook ест твои данные',
                'image' => 'images/article-1.jpeg',
                'slug'  => 'facebook-eat-your-data',
            ],
            [
                'title' => 'Когда пролил кофе на клавиатуру',
                'image' => 'images/article-3.jpg',
                'slug'  => 'what-to-do-if-need-fronted',
            ],
            [
                'title' => 'Что делать, если надо верстать?',
                'image' => 'images/article-2.jpeg',
                'slug'  => 'what-to-do-if-need-fronted',
            ],
            [
                'title' => 'Когда пролил кофе на клавиатуру',
                'image' => 'images/article-3.jpg',
                'slug'  => 'what-to-do-if-need-fronted',
            ],
        ];
    }
    
    public function article()
    {
        $articles = $this->articles();
        return $articles[rand(0, count($articles) - 1)];
    }
}
