<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use App\Service\RegistrationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        GuardAuthenticatorHandler $guard,
        LoginFormAuthenticator $authenticator,
        RegistrationService $registrationService
    ) {

        $error = '';

        if ($request->isMethod('POST')) {

            try {
                $user = $registrationService->register($request);
            } catch (CustomUserMessageAuthenticationException $e) {
                $error = $e->getMessage();
            }

            if(empty($error)) {
                return $guard->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'
                );
            }
        }

        return $this->render(
            'diplom/security/register.html.twig',
            [
                'error' => $error,
            ]
        );
    }
}
