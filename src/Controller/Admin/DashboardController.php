<?php

namespace App\Controller\Admin;

use App\Entity\Tn;
use App\Entity\Jobcoach;
use App\Entity\Fm;
use App\Entity\Termin;
use App\Entity\TerminType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(TnCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BeSt Project');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Back to Home', 'fa fa-home', 'app_app');
        yield MenuItem::linkToCrud('TN', 'fas fa-user', Tn::class);
        yield MenuItem::linkToCrud('Jobcoach', 'fas fa-users', Jobcoach::class);
        yield MenuItem::linkToCrud('FM', 'fas fa-users', Fm::class);
        yield MenuItem::linkToCrud('Termine','fa fa-calendar-check-o', Termin::class);
        
        //yield MenuItem::linkToCrud('Termine - types','fa fa-calendar-o', TerminType::class);
    }
}
