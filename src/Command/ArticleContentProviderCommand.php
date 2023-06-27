<?php

namespace App\Command;

use App\Service\ArticleContentProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArticleContentProviderCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:article:content_provider';
    /**
     * @var ArticleContentProviderInterface
     */
    private ArticleContentProviderInterface $articleContentProvider;

    /**
     * ArticleContentProviderCommand constructor.
     * @param ArticleContentProviderInterface $articleContentProvider
     */
    public function __construct(ArticleContentProviderInterface $articleContentProvider)
    {
        $this->articleContentProvider = $articleContentProvider;
        parent::__construct();
    }

    /**
     * Команда для вставки ключевых слов из параметров в команде
     * paragraphs - Количество параграфов
     * word - Вставляемое слово
     * wordsCount - Количество вставляемых слов
     */
    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('paragraphs', InputArgument::REQUIRED, 'Количество параграфов')
            ->addArgument('word', InputArgument::OPTIONAL, 'Вставляемое слово')
            ->addArgument('wordsCount', InputArgument::OPTIONAL, 'Количество вставляемых слов');
    }

    /**
     * Команда для выполнения операции
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int 0
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $paragraphs = (int)$input->getArgument('paragraphs');
        $paragraphs = ($paragraphs?0:$paragraphs);

        $word = $input->getArgument('word');
        $word = ($word?'':$word);

        $wordsCount = (int)$input->getArgument('wordsCount');
        $wordsCount = ($wordsCount?0:$wordsCount);

        $output->writeln($this->articleContentProvider->get($paragraphs, $word, $wordsCount));

        return 0;
    }
}
