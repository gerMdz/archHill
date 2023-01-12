<?php

namespace App\Services;

use App\Traits\AuthorizesMarketRequests, App\Traits\ConsumesExternalService, App\Traits\InteractsWithMarketResponses;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class MarketServices
{
    use ConsumesExternalService;
    use InteractsWithMarketResponses;
    use AuthorizesMarketRequests;

    protected string $baseUri;
    private $passwordClientSecret;
    private $clientId;
    private $clientSecret;
    private $passwordClientId;

    /**
     * @param $baseUri
     * @param $passwordClientSecret
     * @param $clientId
     * @param $clientSecret
     * @param $passwordClientId
     */
    public function __construct($baseUri, $passwordClientSecret, $clientId, $clientSecret, $passwordClientId)
    {
        $this->baseUri = $baseUri;
        $this->passwordClientSecret = $passwordClientSecret;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->passwordClientId = $passwordClientId;
    }

    /**
     * Obtiene lista de productos desde una api
     * @return string
     * @throws GuzzleException
     */
    public function getProducts(): string
    {
        return $this->makeRequest('Get', 'products');
    }


}