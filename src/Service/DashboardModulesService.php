<?php

namespace App\Service;

use App\Entity\TextModule;
use App\Repository\TextModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DashboardModulesService
{
    /**
     * @var TextModuleRepository
     */
    private $textModuleRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * DashboardModulesService constructor.
     *
     * @param TextModuleRepository $textModuleRepository
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     */
    public function __construct(TextModuleRepository $textModuleRepository, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->textModuleRepository = $textModuleRepository;
        $this->em = $em;
        $this->paginator = $paginator;
    }

    /**
     * Update text modules
     *
     * @param Request $request
     * @return string|null
     */
    public function updateModules(Request $request): ?string
    {
        if(!empty($request->query->get('del')))
        {
            $repository = $this->em->getRepository(TextModule::class);
            $textModule = $repository->find($request->query->get('del'));
            $this->em->remove($textModule);
            $this->em->flush();

            return 'Модуль успешно удален';
        }

        if(!empty($request->request->get('articleTitle')) && !empty($request->request->get('articleWord')))
        {
            $textModule = new TextModule();
            $textModule->setName($request->request->get('articleTitle'));
            $textModule->setContent($request->request->get('articleWord'));

            $this->em->persist($textModule);
            $this->em->flush();

            return 'Модуль успешно добавлен';
        }

        return null;
    }

    /**
     * Get text modules with pagination
     *
     * @param Request $request
     * @return array
     */
    public function getModules(Request $request): array
    {
        $pagination = $this->paginator->paginate(
            $this->textModuleRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 4)
        );

        return [
            'pagination' => $pagination,
        ];
    }
}