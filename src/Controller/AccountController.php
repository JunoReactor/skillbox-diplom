<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Service\Account;
use App\Service\DashboardModulesService;
use App\Service\SubscriptionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @method User|null getUser()
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account/dashboard", name="app_account_dashboard")
     *
     * This method handles the account dashboard page route.
     *
     * @param Account $account
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboard(Account $account): \Symfony\Component\HttpFoundation\Response
    {
        $data = $account->dashboardIndex();
        return $this->render('diplom/account/dashboard.html.twig', [
            'articles'      => $data['articles'],
            'textModules'   => $data['textModules']
        ]);
    }

    /**
     * @Route("/account/dashboard/subscription", name="app_account_dashboard_subscription")
     *
     * This method handles the account subscription page route.
     *
     * @param Request $request
     * @param SubscriptionService $subscriptionService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardSubscription(Request $request, SubscriptionService $subscriptionService): \Symfony\Component\HttpFoundation\Response
    {
        $subscription = $subscriptionService->handleSubscription($request);
        return $this->render('diplom/account/dashboard_subscription.html.twig', [
            'subscription' => $subscription
        ]);
    }

    /**
     * @Route("/account/dashboard/profile", name="app_account_dashboard_profile")
     *
     * This method handles the account profile page route.
     *
     * @param Request $request
     * @param Account $account
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardProfile(Request $request, Account $account): \Symfony\Component\HttpFoundation\Response
    {
        $user = $this->getUser();
        $data = $account->dashboardProfile($user, $request);
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
     *
     * This method handles the account modules page route.
     *
     * @param Request $request
     * @param DashboardModulesService $dashboardModulesService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardModules(Request $request, DashboardModulesService $dashboardModulesService): \Symfony\Component\HttpFoundation\Response
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
     *
     * This method handles the account history page route.
     *
     * @param Request $request
     * @param Account $account
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardHistory(Request $request, Account $account): \Symfony\Component\HttpFoundation\Response
    {
        $pagination = $account->dashboardHistory($request);
        return $this->render('diplom/account/dashboard_history.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/account/dashboard/article/detail/{id}", name="app_account_dashboard_article_detail")
     *
     * This method handles the account article detail page route.
     *
     * @param Article $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardArticleDetail(Article $article): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('diplom/account/dashboard_article_detail.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/account/dashboard/create_article", name="app_account_dashboard_create_article")
     *
     * This method handles the account create article page route.
     *
     * @param Request $request
     * @param DashboardModulesService $articleService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardCreateArticle(Request $request, DashboardModulesService $articleService): \Symfony\Component\HttpFoundation\Response
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
