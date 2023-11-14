<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends WebController
{
    #[Route(path: '/', name: 'homepage', methods: ['GET', 'POST'])]
    public function home(): Response
    {
        return $this->render('default/homepage.html.twig');
    }
}
