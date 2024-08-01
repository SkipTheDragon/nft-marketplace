<?php

namespace App\Controller;

use Rompetomp\InertiaBundle\Architecture\InertiaTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class MainController
{
    use InertiaTrait;

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->inertia->render('Main');
    }
}
