<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Traits\ConsumesExternalService;
use App\Traits\InteractsWithMarketResponses;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MarketAuthenticationService
{

    protected string $baseUri;
    private $passwordClientSecret;
    private $clientId;
    private $clientSecret;
    private $passwordClientId;
    private $base_token;
    private RequestStack $session;
    private UserRepository $userRepository;

    use ConsumesExternalService;
    use InteractsWithMarketResponses;


    /**
     * @param $baseUri
     * @param $passwordClientSecret
     * @param $clientId
     * @param $clientSecret
     * @param $passwordClientId
     * @param $base_token
     * @param RequestStack $session
     * @param UserRepository $userRepository
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        $baseUri, $passwordClientSecret, $clientId,
        $clientSecret, $passwordClientId, $base_token,
        RequestStack $session, UserRepository $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
        $this->baseUri = $baseUri;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->passwordClientId = $passwordClientId;
        $this->passwordClientSecret = $passwordClientSecret;
        $this->base_token = $base_token;
        $this->session = $session;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws GuzzleException
     */
    public function getClientCredentialsToken()
    {
        if ($token = $this->existingValidClientCredentialsToken()) {
            return $token;
        }

        $formParams = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $tokenData = $this->makeRequest('POST', 'oauth/token', [], $formParams);

        $this->storeValidToken($tokenData, 'client_credentials');

        return $tokenData->access_token;
    }

    /**
     * @return string
     */
    public function resolveAuthorizationUrl(): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->urlGenerator->generate('app_authorization', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'response_type' => 'code',
            'scope' => 'purchase-product manage-products manage-account read-general'
        ]);

//        dd($query);

        return "{$this->baseUri}/oauth/authorize?{$query}";
    }

    /**
     * @param string $code
     * @return stdClass
     * @throws GuzzleException
     */
    public function getCodeToken(string $code): stdClass
    {
        $formParams = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->urlGenerator->generate('app_authorization'),
            'code' => $code
        ];

        $tokenData = $this->makeRequest('POST', 'oauth/token', [], $formParams);


        $this->storeValidToken($tokenData, 'authorization_code');

        return $tokenData;
    }

    /**
     * @param string $username
     * @param string $password
     * @return stdClass
     * @throws GuzzleException
     */
    public function getPasswordToken(string $username, string $password): stdClass
    {
        $formParams = [
            'grant_type' => 'password',
            'client_id' => $this->passwordClientId,
            'client_secret' => $this->passwordClientSecret,
            'username' => $username,
            'password' => $password,
            'scope' => 'purchase-product manage-products manage-account read-general'
        ];

        $tokenData = $this->makeRequest('POST', 'oauth/token', [], $formParams);

        $this->storeValidToken($tokenData, 'password');

        return $tokenData;
    }


    /**
     * @param stdClass $tokenData
     * @param string $grantTYpe
     * @return void
     */
    private function storeValidToken(stdClass $tokenData, string $grantTYpe): void
    {
        $now = time();
        $tokenData->token_expires_at = $now - ($tokenData->expires_in - 5);
        $tokenData->access_token = "{$tokenData->token_type} {$tokenData->access_token}";
        $tokenData->grant_type = $grantTYpe;

        $this->session->getSession()->set('current_token', $tokenData);
    }

    /**
     * @return string|boolean
     */
    public function existingValidClientCredentialsToken(): bool|string
    {
        $now = time();
        if ($this->session->getSession()->has('current_token')) {
            $tokenData = $this->session->getSession()->get('current_token');
            if ($now < $tokenData->token_expires_at) {
                return $tokenData->access_token;
            }
        }
        return false;
    }

    /**
     * Obtiene un access token desde un usuario autenticado
     *
     * @throws GuzzleException
     */
    public function getAuthenticatedUserToken()
    {
        /** @var User $user */
        $user = $this->session->getCurrentRequest()->getUser();


        if ($user->getAccessToken() !== null
            && $user->getTokenExpiresAt() > new \DateTimeImmutable()) {
            return $user->getAccessToken();
        }

        return $this->refreshAuthenticatedUserToken($user);


    }

    /**
     * @param User $user
     * @return string|null
     * @throws GuzzleException
     */
    public function refreshAuthenticatedUserToken(User $user): string|null
    {

        $clientId = $this->clientId;
        $clientSecret = $this->clientSecret;

        if ($user->getGrantType() == 'password') {
            $clientId = $this->passwordClientId;
            $clientSecret = $this->passwordClientSecret;
        }


        $formParams = [
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $user->getRefreshToken(),
        ];

        $tokenData = $this->makeRequest('POST', 'oauth/token', [], $formParams);

        $this->storeValidToken($tokenData, $user->getGrantType());

        $user->setAccessToken($tokenData['access_token']);
        $user->setRefreshToken($tokenData['refresh_token']);
        $user->setTokenExpiresAt($tokenData['token_expires_at']);
        $this->userRepository->save($user);

        return $user->getAccessToken();
    }

}