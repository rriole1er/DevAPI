<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Handler\LivrePublishHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateLivrePublication extends AbstractController
{

    private $bookPublishHandler;

    public function __construct(LivrePublishHandler $bookPublishHandler){

        $this->bookPublishHandler = $bookPublishHandler;
    }

    public function __invoke(Livre $data): Livre
    {

        return $this->bookPublishHandler->handle($data);
    }

}