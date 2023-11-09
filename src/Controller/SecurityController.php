<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\MarketAuthenticationService;
use App\Services\MarketServices;
use App\Services\UserService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private MarketAuthenticationService $marketAuthenticationService;
    private MarketServices $marketServices;
    private UserService $userService;

    /**
     * @param MarketAuthenticationService $marketAuthenticationService
     * @param MarketServices $marketServices
     * @param UserService $userService
     */
    public function __construct(MarketAuthenticationService $marketAuthenticationService, MarketServices $marketServices, UserService $userService)
    {
        $this->marketAuthenticationService = $marketAuthenticationService;
        $this->marketServices = $marketServices;
        $this->userService = $userService;
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

        $data = 'Se canceló el proceso';

        try {

            if ($request->query->has('code')) {

//                $tokenData = $this->marketAuthenticationService->getCodeToken($request->query->get('code'));
                $tokenData = $this->marketAuthenticationService->getClientCredentialsToken();

                $userData = $this->marketServices->getUserInformation();

                $user = $this->registerOrUpdate($userData, $tokenData);

                $this->loginUser($user);


                return $this->redirectToRoute('target_path');


            }
        } catch (ClientException $exception){
            $mensaje = $exception->getResponse()->getBody();
            dd($exception);

            if(in_array('invalid_credentials', $mensaje)){
                $data = $mensaje;
            }
            throw $exception;

        }
        $lastUsername = $authenticationUtils->getLastUsername();
        $authorizationUri = $this->marketAuthenticationService->resolveAuthorizationUrl();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => [
                    'data' => $data,
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

    /**
     * @param $userDara
     * @param $tokenData
     * @return User
     */
    public function registerOrUpdate(\stdClass $userData, $tokenData): User
    {

        return $this->userService->updateOrCreate(
            ['service_id' => $userData->identifier],
            [
                'grantType' => $tokenData->grantType,
                'refreshToken' => $tokenData->refreshToken,
                'tokenExpiresAt' => $tokenData->tokenExpiresAt,
            ]
        );


    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function loginUser(User $user, $remember = true):void
    {
        $this->container->get('request_stack')->getSession()->migrate();

    }
}
