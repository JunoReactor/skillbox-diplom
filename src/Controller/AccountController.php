<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\TextModule;
use App\Entity\User;
use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\TextModuleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Faker\Factory;
use Faker\Generator;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @method User|null getUser()
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account/dashboard", name="app_account_dashboard")
     */
    public function dashboard()
    {
        return $this->render('diplom/account/dashboard.html.twig', []);
    }

    /**
     * @Route("/account/dashboard/subscription", name="app_account_dashboard_subscription")
     */
    public function dashboard_subscription()
    {
        return $this->render('diplom/account/dashboard_subscription.html.twig', []);
    }

    /**
     * @Route("/account/dashboard/profile", name="app_account_dashboard_profile")
     */
    public function dashboard_profile(Security $security, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {

        //$user = $security->getUser();
        //dd($user->);
        $user = $this->getUser();
        //dd($user->getEmail());

        $apiToken = new ApiToken($user);
        //$apiToken->isExpired();
        //$user->getApiTokens();

        $token = '';
        if(!empty($request->get('newToken')))
        {
            $this->addFlash('flash_message', 'API токен успешно изменен');
            $token = md5(time());
            //dd($apiToken);
            $user->addApiToken($apiToken);

            $em->persist($apiToken);
            $em->persist($user);
            $em->flush();
        }

        /*$form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userModel = $form->getData();

            $user = new User();

            $user
                ->setEmail($userModel->email)
                ->setFirstName($userModel->firstName)
                ->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $userModel->plainPassword
                ))
            ;

            $em->persist($user);
            $em->flush();
        }*/

        $inputName              = $request->request->get('inputName');
        $inputEmail             = $request->request->get('inputEmail');
        $inputPassword          = $request->request->get('inputPassword');
        $inputConfirmPassword   = $request->request->get('inputConfirmPassword');

        if ($request->getMethod() == 'POST' && $inputPassword == $inputConfirmPassword && !empty($inputPassword) )
        {

            $this->addFlash('flash_message', 'Профиль успешно изменен');

            $user->setFirstName($inputName);
            $user->setEmail($inputEmail);
            $user->setPassword($passwordEncoder->encodePassword($user,$inputPassword));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

        }


        //$user->getApiTokens();
       // $user->

        return $this->render('diplom/account/dashboard_profile.html.twig', [
            'API_token'     => $token,
            'name'          => $user->getFirstName(),
            'email'         => $user->getEmail(),
        ]);
    }

    /**
     * @Route("/account/dashboard/modules", name="app_account_dashboard_modules")
     */
    public function dashboard_modules(TextModuleRepository $textModuleRepository, Request $request, EntityManagerInterface $em, PaginatorInterface $paginator)
    {

        if(!empty($request->query->get('del')))
        {
            //$textModule = new TextModule();
            //$textModule->getId($request->query->get('del'));

            $this->addFlash('flash_message', 'Модуль успешно удален');

            $repository = $em->getRepository(TextModule::class);
            $textModule = $repository->find($request->query->get('del'));
            $em->remove($textModule);
            $em->flush();
        }

        //print_r($_POST);
        //dd($request->request->get('articleTitle'));

        if(
            !empty($request->request->get('articleTitle'))
            && !empty($request->request->get('articleWord'))
        )
        {
            $this->addFlash('flash_message', 'Модуль успешно добавлен');

            $textModule = new TextModule();
            $textModule->setName($request->request->get('articleTitle'));
            $textModule->setContent($request->request->get('articleWord'));

            $em->persist($textModule);
            $em->flush();
           // dd($textModule->getId());
        }

        $textModule = new TextModule();

        $pagination = $paginator->paginate(
            //$articleRepository->latest(),
            //$textModuleRepository->findAllWithSearchQuery($request->query->get('q'), $request->query->has('showDeleted')),
            $textModuleRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 4)
        );

        return $this->render('diplom/account/dashboard_modules.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/account/dashboard/history", name="app_account_dashboard_history")
     */
    public function dashboard_history(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            //$articleRepository->latest(),
            $articleRepository->findAllWithSearchQuery($request->query->get('q'), $request->query->has('showDeleted')),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );

        return $this->render('diplom/account/dashboard_history.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/account/dashboard/create_article", name="app_account_dashboard_create_article")
     */
    public function dashboard_create_article(EntityManagerInterface $em, Request $request)
    {

        if(count($request->request) ==  12)
        {
            dd($request->request);
        }

       /*   "fieldTheme" => "-"
            "articleTitle" => "Тестовая статья"
            "article0Word" => "EXAMPLEКлючевое слово"
            "article1Word" => "EXAMPLEодительный падеж"
            "article2Word" => "EXAMPLESМножественное число"
            "articleSizeFrom" => "11"
            "articleSizeTo" => "14"
            "word1Field" => "Продвигаемое слово"
            "word1CountField" => "21"
            "word2Field" => "Продвигаемое слово2"
            "word2CountField" => "33"
            "img" => ""*/

        $faker = Factory::create();

        //dd($faker->paragraph());

        $articleSizeFrom = $request->request->get('articleSizeFrom');
        $articleSizeTo = $request->request->get('articleSizeTo');
        $word1Field = $request->request->get('word1Field');
        $word1Field = $request->request->get('word1Field');

        dd($faker->realText($faker->numberBetween(0, 30)));

        $form = $this->createForm(ArticleFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /** Article $article */
            $article = $form->getData();
            /*$article
                ->setAuthor($this->getUser())
                ->setPublishedAt(new \DateTime())
            ;*/

            $em->persist($article);
            $em->flush();  

            $this->addFlash('flash_message','Статья добавлена');
            return $this->redirectToRoute('app_account_dashboard_create_article');      
        }

        return $this->render('diplom/account/dashboard_create_article.html.twig', [
           // 'articleForm' => $form->createView(),
            'articleTextData' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/dashboard/article_detail", name="app_account_dashboard_article_detail")
     */
    public function dashboard_article_detail()
    {
        return $this->render('diplom/account/dashboard_article_detail.html.twig', []);
    }

}
