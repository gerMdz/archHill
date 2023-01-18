<?php

namespace App\Services;

use App\Traits\AuthorizesMarketRequests, App\Traits\ConsumesExternalService, App\Traits\InteractsWithMarketResponses;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class MarketAuthenticationService
{

    protected string $baseUri;
    private $passwordClientSecret;
    private $clientId;
    private $clientSecret;
    private $passwordClientId;
    private $base_token;

    use ConsumesExternalService;
    use InteractsWithMarketResponses;
    use AuthorizesMarketRequests;

    /**
     * @param $baseUri
     * @param $passwordClientSecret
     * @param $clientId
     * @param $clientSecret
     * @param $passwordClientId
     * @param $base_token
     */
    public function __construct($baseUri, $passwordClientSecret, $clientId, $clientSecret, $passwordClientId, $base_token)
    {
        $this->baseUri = $baseUri;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->passwordClientId = $passwordClientId;
        $this->passwordClientSecret = $passwordClientSecret;
        $this->base_token = $base_token;
    }

    public function getClientCredentialsToken()
    {
        $formParams =[
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
        ];
        $tokenData = $this->makeRequest('POST', 'oauth/token', [], $formParams);

        return "{$tokenData->token_type} {$tokenData->access_token}";
   }

}