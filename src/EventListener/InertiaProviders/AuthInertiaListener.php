<?php

namespace App\EventListener\InertiaProviders;

use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Provides the user data to the Inertia.js frontend.
 */
final readonly class AuthInertiaListener
{
    public function __construct(
        private InertiaInterface $inertia,
        private Security $security
    )
    {
    }

    #[AsEventListener(event: KernelEvents::CONTROLLER)]
    public function onKernelController(ControllerEvent $event): void
    {
        $this->inertia->share('GLOBALS::AUTH', [
            'user' => $this->security->getUser() ?? null,
        ]);
    }
}
