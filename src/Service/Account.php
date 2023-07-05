<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Account
{
    /**
     * Dashboard profile for user
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return array
     */
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