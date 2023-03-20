<?php

namespace App\Handler;

use App\Entity\Livre;

class LivrePublishHandler
{
    public function handle(Livre $data): Livre
    {
        $data->setIsPublished(true);
        return $data;
    }
}