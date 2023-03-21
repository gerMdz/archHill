<?php

namespace App\Controller;

use App\Services\MarketAuthenticationService;
use App\Services\MarketServices;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private MarketAuthenticationService $marketAuthenticationService;
    private MarketServices $marketServices;

    /**
     * @param MarketAuthenticationService $marketAuthenticationService
     * @param MarketServices $marketServices
     */
    public function __construct(MarketAuthenticationService $marketAuthenticationService, MarketServices $marketServices)
    {
        $this->marketAuthenticationService = $marketAuthenticationService;
        $this->marketServices = $marketServices;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        $authorizationUri = $this->marketAuthenticationService->resolveAuthorizationUrl();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            ['last_username' => $lastUsername, 'error' => $error, 'authorizationUri' => $authorizationUri]
        );
    }

    /**
     * @throws GuzzleException
     */
    #[Route(path: '/oidc/callback', name: 'app_authorization')]
    public function authorization(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if ($request->query->has('code')) {
            $tokenData = $this->marketAuthenticationService->getCodeToken($request->query->get('code'));

            $userData = $this->marketServices->getUserInformation();

            dd($userData);

            return;
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        $authorizationUri = $this->marketAuthenticationService->resolveAuthorizationUrl();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => [
                    'data' => 'Se canceló el proceso',
                ],
                'authorizationUri' => $authorizationUri,
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
