<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserDeactivateCommand extends Command
{
    protected static $defaultName = 'app:user:deactivate';
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UserDeactivateCommand constructor.
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->em = $em;
    }


    protected function configure()
    {
        $this
            ->setDescription('Деактивирует или активирует пользователя')
            ->addArgument('userId', InputArgument::OPTIONAL, 'Id Пользователя')
            ->addOption('reverse', null, InputOption::VALUE_NONE, 'Выполнить активацию')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userId = $input->getArgument('userId');

        if (! $userId) {
            $io->error('Не указан id пользователя');
            return 1;
        }
        
        $user = $this->userRepository->find($userId);
        
        if (! $user) {
            $io->error("Пользователь с id={$userId} не найден");
            return 1;
        }
        
        $isActive = $input->getOption('reverse');
        
        $user->setIsActive($isActive);

        $this->em->flush();

        $io->success('Пользователь успешно ' . ($isActive ? 'активирован' : 'деактивирован'));

        return 0;
    }
}
