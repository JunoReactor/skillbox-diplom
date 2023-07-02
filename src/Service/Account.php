<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\Article;
use App\Entity\TextModule;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\TextModuleRepository;
use App\Service\ArticleContentProvider;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class Account
{
    public function dashboard_profile(User $user, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $apiToken = new ApiToken($user);

        $token = '';
        if(!empty($request->get('newToken')))
        {
            $token = md5(time());
            $user->addApiToken($apiToken);

            $em->persist($apiToken);
            $em->persist($user);
            $em->flush();
        }

        $inputName              = $request->request->get('inputName');
        $inputEmail             = $request->request->get('inputEmail');
        $inputPassword          = $request->request->get('inputPassword');
        $inputConfirmPassword   = $request->request->get('inputConfirmPassword');

        if ($request->getMethod() == 'POST' && $inputPassword == $inputConfirmPassword && !empty($inputPassword) )
        {
            $user->setFirstName($inputName);
            $user->setEmail($inputEmail);
            $user->setPassword($passwordEncoder->encodePassword($user,$inputPassword));

            $em->persist($user);
            $em->flush();
        }

        return [
            'API_token'     => $token,
            'name'          => $user->getFirstName(),
            'email'         => $user->getEmail(),
        ];
    }

}