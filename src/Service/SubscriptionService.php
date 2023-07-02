<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handleSubscription(Request $request)
    {
        $user = $this->getUser();
        $date = new \DateTime('@'.strtotime('now'));
        $subscriptionDate = $user->getSubscriptionDate();
        $dateInAWeek = new \DateTime("+1 week");

        if($subscriptionDate != null):
            if($subscriptionDate >= $dateInAWeek)
            {
                $user->setSubscription('Free');
                $user->setSubscriptionDate(null);
                $user->setIsActive(0);
                $this->em->persist($user);
                $this->em->flush();
            }
        endif;

        if($user->getSubscription() == '')
        {

            $user->setSubscription('Free');
            $user->setSubscriptionDate(null);
            $user->setIsActive(0);
            $this->em->persist($user);
            $this->em->flush();
        }

        if($request->query->get('get') == 'Pro')
        {
            $user->setSubscription('Pro');
            $user->setSubscriptionDate($date);
            $user->setIsActive(1);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('flash_message', 'Подписка Pro оформлена, до '.$dateInAWeek->format('Y-m-d'));
            // return $this->redirect($request->getPathInfo());
        }

        if($request->query->get('get') == 'Plus')
        {
            $user->setSubscription('Plus');
            $user->setSubscriptionDate($date);
            $user->setIsActive(1);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('flash_message', 'Подписка Plus оформлена, до '.$dateInAWeek->format('Y-m-d'));
            //return $this->redirect($request->getPathInfo());
        }

        //dd($request->query->get('get'));
        return $user->getSubscription();
    }
}
