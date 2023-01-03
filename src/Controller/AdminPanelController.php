<?php

namespace App\Controller;

use App\Service\SessionService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPanelController extends AbstractController
{
    private $user;
    private $isLogged;

    /**
     * @Route("/admin/panel", name="app_admin_panel")
     */
    public function index(SessionService $sessionService): Response
    {
        if($sessionService->checkConditions())
        {
            echo "Sie haben keine Berechtigung";
    
            echo "<p><a href='/'>Zurück</a></p>";

            die();
        }

        return $this->render('admin_panel/index.html.twig', [
            'controller_name' => 'AdminPanelController',
        ]);
    }
}
