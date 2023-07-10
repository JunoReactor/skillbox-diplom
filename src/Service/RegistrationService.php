<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class RegistrationService
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function register(Request $request)
    {
        if($request->request->get('password') != $request->request->get('confirmPassword'))
        {
            throw new CustomUserMessageAuthenticationException(
                'Пароли не совпадают'
            );
        }

        $user = new User();
        $user
            ->setEmail($request->request->get('email'))
            ->setFirstName($request->request->get('firstName'))
            ->setPassword($this->passwordEncoder->encodePassword($user, $request->request->get('password')));

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
