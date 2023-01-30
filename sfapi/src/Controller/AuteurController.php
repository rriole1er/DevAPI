<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuteurController extends AbstractController
{

    /**
     * @Route("/api/auteurs", name="app_auteurs_api",methods={"GET"})
     */
    public function getAuteurs(AuteurRepository $auteurRepository,SerializerInterface $serializer) : Response
    {
        $auteurs = $auteurRepository->findAll();
        $auteursJson= $serializer->serialize($auteurs,'json');
        return new JsonResponse($auteursJson,200,[],true);

    }

    /**
     * @Route("/api/auteurs/{id}",
     *     name="app_auteur_api",
     *     methods={"GET"})
     */
    public function getAuteur(Auteur $auteur, SerializerInterface $serializer) : Response
    {
        $auteurJson= $serializer->serialize($auteur,'json');
        return new JsonResponse($auteurJson,200,[],true);
    }


    /**
     * @Route("/api/auteurs/",
     *     name="post_auteur_api",
     *     methods={"POST"})
     */
    public function postAuteur(Request $request,SerializerInterface $serializer, AuteurRepository $repository ) : Response{

        $data = $request->getContent();
        $auteur = $serializer->deserialize($data,Auteur::class,'json');
        $repository->add($auteur,true);
        return new JsonResponse("",Response::HTTP_CREATED,[],true);
    }

    /**
     * @Route("/api/auteurs/",
     *     name="delete_auteur_api",
     *     methods={"DELETE"})
     */
    public function deleteAuteur(Auteur $auteur, AuteurRepository $repository) : Response{

        $repository->remove($auteur,true);
        return new JsonResponse("",Response::HTTP_OK,[],true);
    }
}


