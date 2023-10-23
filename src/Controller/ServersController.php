<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/',name: 'home')]
class ServersController extends AbstractController
{

    #[Route('', name:'home_show')]
    public function show(): Response
    {
        return $this->render('servers.html.twig');
    }
}