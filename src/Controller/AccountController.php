<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Article;
use App\Entity\TextModule;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\TextModuleRepository;
use App\Service\Account;
use App\Service\ArticleContentProvider;
use App\Service\DashboardModulesService;
use App\Service\SubscriptionService;
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

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @method User|null getUser()
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account/dashboard", name="app_account_dashboard")
     */
    public function dashboard(ArticleRepository $articleRepository, TextModuleRepository $textModuleRepository)
    {

        return $this->render('diplom/account/dashboard.html.twig', [
            'articles' => $articleRepository->getCountFull(),
            'textModules' => $articleRepository->getCountMonth()
        ]);
    }

    /**
     * @Route("/account/dashboard/subscription", name="app_account_dashboard_subscription")
     */
    public function dashboardSubscription(Request $request, EntityManagerInterface $em, SubscriptionService $subscriptionService)
    {
        $this->subscriptionService->handleSubscription($request);
        return $this->render('diplom/account/dashboard_subscription.html.twig', [
            'subscription' => $subscription
        ]);
    }

    /**
     * @Route("/account/dashboard/profile", name="app_account_dashboard_profile")
     */
    public function dashboardProfile(
        Request $request,
        Account $account,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {

        $user = $this->getUser();
        $data = $account->dashboardProfile($user, $request, $em, $passwordEncoder);

        if(!empty($data['API_token']))
        {
            $this->addFlash('flash_message', 'API токен успешно изменен');
        }
        else if(!empty($data['name']) || !empty($data['email']))
        {
            $this->addFlash('flash_message', 'Профиль успешно изменен');
        }

        return $this->render('diplom/account/dashboard_profile.html.twig', $data);

    }

    /**
     * @Route("/account/dashboard/modules", name="app_account_dashboard_modules")
     */
    public function dashboardModules(Request $request, DashboardModulesService $dashboardModulesService)
    {
        $flashMessage = $dashboardModulesService->updateModules($request);
        if ($flashMessage !== null) {
            $this->addFlash('flash_message', $flashMessage);
        }

        $data = $dashboardModulesService->getModules($request);

        return $this->render('diplom/account/dashboard_modules.html.twig', $data);
    }

    /**
     * @Route("/account/dashboard/history", name="app_account_dashboard_history")
     */
    public function dashboardHistory(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            //$articleRepository->latest(),
            $articleRepository->findAllWithSearchQuery($request->query->get('q'), $request->query->has('showDeleted')),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render('diplom/account/dashboard_history.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/account/dashboard/article/detail/{id}", name="app_account_dashboard_article_detail")
     */
    public function dashboardArticleDetail(Article $article)
    {

        return $this->render('diplom/account/dashboard_article_detail.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/account/dashboard/create_article", name="app_account_dashboard_create_article")
     */
    public function dashboardCreateArticle(Request $request, DashboardModulesService $articleService)
    {
        if ($message = $articleService->createArticle($request))
        {
            $this->addFlash('flash_message', $message);
        }

        return $this->render('diplom/account/dashboard_create_article.html.twig', [
            'articleTextData' => '',
        ]);
    }

}
