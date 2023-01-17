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
    private $base_token;

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
        $this->passwordClientSecret = $passwordClientSecret;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->passwordClientId = $passwordClientId;
        $this->base_token = $base_token;
    }

    /**
     * Obtiene lista de productos desde una api
     * @return string
     * @throws GuzzleException
     */
    public function getProducts()
    {
        return $this->makeRequest('GET', 'products');
    }

    /**
     * @throws GuzzleException
     */
    public function getProduct($id)
    {
        return $this->makeRequest('GET', "products/{$id}");
    }

    public function getCategories()
    {
        return $this->makeRequest('GET', 'categories');
    }

    public function getCategoryProducts($id)
    {
        return $this->makeRequest('GET', "categories/{$id}/products");
    }


}