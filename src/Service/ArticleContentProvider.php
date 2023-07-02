<?php

namespace App\Service;

class ArticleContentProvider
{
    /**
     * @var PasteWords
     */
    private $pasteWords;


    public function __construct(PasteWords $pasteWords)
    {
        $this->pasteWords = $pasteWords;
    }

    /**
     * Функция для обработки текста статьи
     * @param string $text входящий текст
     * @param string $word слово
     * @param int $wordsCount количество слов
     * @return string $text текста статьи
     */
    public function get(string $text, string $word = null, int $wordsCount = 0): string
    {
        if ($word && $wordsCount) {
            for ($i = 0; $i < $wordsCount; $i++)
            {
                $text = $this->insertRandom($text,$word);
            }
            return $text;
        }
        
        return $text;
    }

    /**
     * Функция для обработки текста параграфа
     * @param string $text входящий текст
     * @param string $word слово
     * @return string $text текста статьи
     */
    private function insertRandom(string $text, string $word)
    {
        $txtArrData = explode(' ', $text);
        $rndElement = rand(0, count($txtArrData)-1);
        $txtArrData[$rndElement] = $txtArrData[$rndElement].' <b>'.$word.'</b>';
        return implode($txtArrData,' ');
    }

}
