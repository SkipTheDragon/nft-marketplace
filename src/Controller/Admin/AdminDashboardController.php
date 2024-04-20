<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Entity\Blockchain;
use App\Entity\NFT;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
         return $this->render('admin/base.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Accounts', 'fas fa-users', Account::class);
        yield MenuItem::linkToCrud('Blockchain', 'fas fa-list', Blockchain::class);
        yield MenuItem::linkToCrud('NFT', 'fas fa-list', NFT::class);
    }
}
