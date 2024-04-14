<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Security\EmailVerifier;
use App\Service\SIWEService;
use App\TransferObject\ConnectDto;
use Rompetomp\InertiaBundle\Architecture\InertiaTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/auth', name: 'app_auth_')]
#[AsController]
class AuthController {
    use InertiaTrait;

    public function __construct(
        private readonly EmailVerifier $emailVerifier,
        private readonly SIWEService $siwe
    )
    {
    }

    #[Route('/connect', name: 'connect', methods: ['GET'])]
    public function connect(): Response
    {
        return $this->inertia->render('auth/Connect', []);
    }

    #[Route('/connect', name: 'connect_post', methods: ['POST'])]
    public function connectPost(
        #[MapRequestPayload]
        ConnectDto $connectDto,
    ): Response
    {
        try {
            $this->siwe->handleConnect($connectDto);
            return new JsonResponse([
                'message' => 'You are now connected.'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Something went wrong, we could not connect you. Please try again.'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/connect/message', name: 'message_to_sign', methods: ['GET'])]
    public function connectMessage(
        Request $request,
    ): Response
    {
        $this->siwe->storeTemporaryMessage($request->query->get('address'), $request->query->get('chainId'));

        return new JsonResponse([
            'message' => $this->siwe->getTemporaryMessage()
        ]);
    }

    #[Route('/onboarding', name: 'onboarding')]
    public function onboarding(Request $request): Response
    {
        return $this->inertia->render('auth/SignIn', []);
    }

    #[Route('/disconnect', name: 'disconnect_confirmation')]
    public function disconnectConfirmation(): Response
    {
        return $this->inertia->render('auth/SignOut', [
            'props' => [],
        ]);
    }

    #[Route('/disconnect/confirmed', name: 'disconnect')]
    public function disconnect(Request $request): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/verify/email', name: 'verify_email')]
    public function verifyAccountEmail(Request $request, TranslatorInterface $translator, AccountRepository $accountRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return new RedirectResponse('connect');
        }

        $account = $accountRepository->find($id);

        if (null === $account) {
            return new RedirectResponse('connect');
        }

        // validate email confirmation link, sets Account::isVerified=true and persists
        $this->emailVerifier->handleEmailConfirmation($request, $account);

        return new RedirectResponse('home');
    }
}
