<?php

namespace App\Security;

use App\Entity\User;
use App\Services\MarketAuthenticationService;
use App\Services\MarketServices;
use App\Services\UserService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppArchHillAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private MarketAuthenticationService $marketAuthenticationService;
    private MarketServices $marketServices;
    private RequestStack $requestStack;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param MarketAuthenticationService $marketAuthenticationService
     * @param MarketServices $marketServices
     * @param RequestStack $requestStack
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator, MarketAuthenticationService $marketAuthenticationService, MarketServices $marketServices, RequestStack $requestStack)
    {
        $this->marketAuthenticationService = $marketAuthenticationService;
        $this->marketServices = $marketServices;
        $this->requestStack = $requestStack;
    }

    public function authenticate(Request $request): Passport
    {

        try {


            $tokenData = $this->marketAuthenticationService->getPasswordToken($this->requestStack->getCurrentRequest()->request->get('email'), $this->requestStack->getCurrentRequest()->request->get('password'));

            $userData = $this->marketServices->getUserInformation();

            $user = $this->registerOrUpdate($userData, $tokenData);

            $this->loginUser($user, $this->requestStack->getCurrentRequest()->request->has('remember'));


//            return $this->redirectToRoute('target_path');

//            if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
//                return new RedirectResponse($targetPath);
//            }
            return true;
        } catch (Exception $exception) {
            $email = $request->request->get('email', '');
//
            $request->getSession()->set(Security::LAST_USERNAME, $email);

            return new Passport(
                new UserBadge($email),
                new PasswordCredentials($request->request->get('password', '')),
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                ]
            );
        }

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_products'));

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function registerOrUpdate(\stdClass $userData, $tokenData, UserService $userService): User
    {

        return $userService->updateOrCreate(
            ['service_id' => $userData->identifier],
            [
                'grantType' => $tokenData->grantType,
                'refreshToken' => $tokenData->refreshToken,
                'tokenExpiresAt' => $tokenData->tokenExpiresAt,
            ]
        );


    }

    public function loginUser(User $user, $remember = true): void
    {
        $this->requestStack->getSession()->migrate();

    }


}
