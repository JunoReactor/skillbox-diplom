<?php

namespace App\Homework;

use Faker\Factory;

class CommentContentProvider
{
    /**
     * @var PasteWords
     */
    private $pasteWords;

    public function __construct(PasteWords $pasteWords)
    {
        $this->pasteWords = $pasteWords;
    }

    public function get(string $word = null, int $wordsCount = 0): string
    {
        $faker = Factory::create();
        
        $text = $faker->paragraph;
        
        if ($word && $wordsCount) {
            $text = $this->pasteWords->paste($text, $word, $wordsCount);
        }
        
        return $text;
    }
}
