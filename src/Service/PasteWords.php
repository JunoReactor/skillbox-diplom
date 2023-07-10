<?php

namespace App\Service;

class PasteWords
{

    /**
     * Функция вставки стова в текст
     * @param string $text текст
     * @param string $word слово
     * @return string $text
     */
    public function paste(string $text, string $word, int $wordsCount = 1): string
    {
        $words = explode(' ', $text);

        for ($i = 0; $i < $wordsCount; $i++) {
            $count = count($words);

            $position = rand(0, $count - 1);

            array_splice($words, $position, 0, $word);
        }

        return implode(' ', $words);
    } 
}
