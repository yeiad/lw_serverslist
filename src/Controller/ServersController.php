<?php

namespace App\Controller;

use App\Service\Servers\ServersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/', name: 'home')]
class ServersController extends AbstractController
{
    public function __construct(private ServersService $serversService)
    {
    }

    #[Route('', name: 'home_show')]
    public function show(): Response
    {
        return $this->render('servers.html.twig', ['servers'=>$this->serversService]);
    }
}