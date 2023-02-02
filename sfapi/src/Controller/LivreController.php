<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\AuteurRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/api/livres/{id}",
     *     name="app_livre_api",
     *     methods={"GET"})
     */
    public function getLivre(Livre $livre, SerializerInterface $serializer) : Response
    {
        $livreJson= $serializer->serialize($livre,'json',['groups' => ['liste_livres']]);
        return new JsonResponse($livreJson,200,[],true);
    }


    /**
     * @Route("/api/livres",
     *     name="post_livre_api",
     *     methods={"POST"})
     */
    public function postLivre(Request $request, LivreRepository $livreRepository, AuteurRepository $auteurRepository){

        $data = $request->toArray();
        $livre = new Livre();
        $livre->setTitre($data["titre"]);
        $livre->setAnnee($data["annee"]);
        $auteur = $auteurRepository->find($data["auteur"]["id"]);
        $livre->setAuteur($auteur);

        $livreRepository->add($livre, true);

        return new JsonResponse("",Response::HTTP_CREATED,[],true);
    }

    /**
     * @Route("/api/livres",
     *     name="delete_livre_api",
     *     methods={"DELETE"})
     */
    public function deleteLivre(Livre $livre, LivreRepository $repository) : Response{

        $repository->remove($livre,true);
        return new JsonResponse("",Response::HTTP_OK,[],true);
    }

}
