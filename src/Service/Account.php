<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Account
{
    private $em;
    private $passwordEncoder;
    private $articleRepository;
    private $paginator;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, ArticleRepository $articleRepository, PaginatorInterface $paginator)
    {
        $this->em                 = $em;
        $this->passwordEncoder    = $passwordEncoder;
        $this->articleRepository  = $articleRepository;
        $this->paginator          = $paginator;
    }

    /**
     * Dashboard
     *
     * @return array
     */
    public function dashboardIndex()
    {
        return [
            'articles' => $this->articleRepository->getCountFull(),
            'textModules' => $this->articleRepository->getCountMonth()
        ];
    }

    /**
     * Dashboard profile for user
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return array
     */
    public function dashboardProfile(User $user, Request $request )
    {
        $apiToken = new ApiToken($user);

        $token = '';
        if(!empty($request->get('newToken')))
        {
            $token = md5(time());
            $user->addApiToken($apiToken);

            $this->em->persist($apiToken);
            $this->em->persist($user);
            $this->em->flush();
        }

        $inputName              = $request->request->get('inputName');
        $inputEmail             = $request->request->get('inputEmail');
        $inputPassword          = $request->request->get('inputPassword');
        $inputConfirmPassword   = $request->request->get('inputConfirmPassword');

        if ($request->getMethod() == 'POST' && $inputPassword == $inputConfirmPassword && !empty($inputPassword) )
        {
            $user->setFirstName($inputName);
            $user->setEmail($inputEmail);
            $user->setPassword($this->passwordEncoder->encodePassword($user,$inputPassword));

            $this->em->persist($user);
            $this->em->flush();
        }

        return [
            'API_token'     => $token,
            'name'          => $user->getFirstName(),
            'email'         => $user->getEmail(),
        ];
    }

    /**
     * Dashboard
     *
     * @param Request $request
     * @return PaginationInterface
     */
    public function dashboardHistory($request)
    {
        $pagination = $this->paginator->paginate(
        //$articleRepository->latest(),
            $this->articleRepository->findAllWithSearchQuery($request->query->get('q'), $request->query->has('showDeleted')),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );
        return $pagination;
    }

}