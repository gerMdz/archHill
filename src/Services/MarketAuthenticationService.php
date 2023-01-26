<?php

namespace App\Services;

use App\Traits\ConsumesExternalService, App\Traits\InteractsWithMarketResponses;
use Exception;
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
     */
    public function __construct(
        $baseUri, $passwordClientSecret, $clientId,
        $clientSecret, $passwordClientId, $base_token,
        RequestStack $session,
        private UrlGeneratorInterface $urlGenerator
    )
    {
        $this->baseUri = $baseUri;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->passwordClientId = $passwordClientId;
        $this->passwordClientSecret = $passwordClientSecret;
        $this->base_token = $base_token;
        $this->session = $session;
    }

    public function getClientCredentialsToken()
    {
        if ($token = $this->existingValidClientCredentialsToken()){
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

        return "{$this->baseUri}/oauth/authorize?{$query}";
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

        $this->session->getSession()->set('current_token',  $tokenData);
    }

    /**
     * @return string|boolean
     */
    public function existingValidClientCredentialsToken(): bool|string
    {
        $now = time();
        if($this->session->getSession()->has('current_token')){
            $tokenData = $this->session->getSession()->get('current_token');
            if($now < $tokenData->token_expires_at){
                return $tokenData->access_token;
            }
        }
        return false;
    }

}