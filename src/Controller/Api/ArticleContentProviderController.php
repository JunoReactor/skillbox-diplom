<?php

namespace App\Controller\Api;

use App\Homework\ArticleContentProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ArticleContentProviderController extends AbstractController
{
    /**
     * @Route("/api/v1/article_content", name="api_aricle_content_provider", methods={"POST"})
     */
    public function index(Request $request, ArticleContentProviderInterface $articleContentProvider, LoggerInterface $apiLogger)
    {
        if (! $this->isGranted("ROLE_API")) {
            $apiLogger->warning("Несанкционированный доступ", [
                'user' => $this->getUser(),
            ]);

            return new JsonResponse([
                'message' => "Несанкционированный доступ",
            ], 401);
        }
    
        $data = json_decode($request->getContent(), true);

        $paragraphs = (int) ($data['paragraphs'] ?? 0);
        $word = $data['word'] ?? null;
        $wordsCount = (int) ($data['wordsCount'] ?? 0);
        
        return $this->json([
            'text' => $articleContentProvider->get($paragraphs, $word, $wordsCount),
        ]);
    }
}
