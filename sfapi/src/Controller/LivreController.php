<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LivreController extends AbstractController
{
    /**
     * @Route("/livre", name="app_livre")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LivreController.php',
        ]);
    }

    /**
     * @Route("/api/livres", name="app_livres_api",methods={"GET"})
     */
    public function getLivres(LivreRepository $livreRepository, SerializerInterface $serializer ) : Response
    {
        $livres = $livreRepository->findAll();
        $livresJson= $serializer->serialize($livres,'json',['groups'=>['liste_livres']]);
        return new JsonResponse($livresJson,200,[],true);

    }
}
