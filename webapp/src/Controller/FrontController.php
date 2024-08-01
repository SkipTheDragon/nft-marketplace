<?php

namespace App\Controller;

use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Service\Attribute\Required;

class FrontController extends AbstractController {
    // Or:
    // use InertiaTrait;

    #[Required]
    public InertiaInterface $inertia;

    #[Route('/home', name: 'home')]
    public function index() {
        return $this->inertia->render('Main');
    }
}
