<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Entity\AccountSession;
use App\Entity\AccountWallet;
use App\Entity\Blockchain;
use App\Entity\Contract;
use App\Entity\NFT;
use App\Entity\RpcProvider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
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
        yield MenuItem::section('Platform', 'fas fa-list');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::subMenu('Accounts', 'fas fa-list')
            ->setSubItems(
                [
                    MenuItem::linkToCrud('Accounts', 'fas fa-list', Account::class),
                    MenuItem::linkToCrud('Sessions', 'fas fa-list', AccountSession::class),
                    MenuItem::linkToCrud('Wallets', 'fas fa-list', AccountWallet::class),
                ]
            );

        yield MenuItem::section('Web3', 'fas fa-list');
        yield MenuItem::subMenu('Networks', 'fas fa-list')
            ->setSubItems(
                [
                    MenuItem::linkToCrud('Blockchains', 'fas fa-list', Blockchain::class),
                    MenuItem::linkToCrud('RPC Providers', 'fas fa-list', RpcProvider::class),
                    MenuItem::linkToCrud('Contract ABIs', 'fas fa-list', Contract::class)
                ]
            );
        yield MenuItem::linkToCrud('NFT', 'fas fa-list', NFT::class);
    }
}
