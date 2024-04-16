<?php

namespace App\EventListener;

use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class InertiaGlobalListener
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
        $this->inertia->share('auth', [
            'user' => $this->security->getUser() ?? null,
        ]);
    }
}
