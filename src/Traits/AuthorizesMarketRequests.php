<?php

namespace App\Traits;

use App\Services\MarketAuthenticationService;


trait AuthorizesMarketRequests
{


    protected MarketAuthenticationService $market_authentication_service;


    /**
     * @param MarketAuthenticationService $market_authentication_service
     * @required

     */
    public function setMarketAuthenticationService(MarketAuthenticationService $market_authentication_service): void
    {
         $this->market_authentication_service = $market_authentication_service;
    }
    /**
     * @param $queryParams
     * @param $formsParams
     * @param $headers
     * @return void
     */
    public function resolveAuthorization(&$queryParams, &$formsParams, &$headers): void
    {

        $accessToken = $this->resolveAccessToken();
        $headers['Authorization'] = $accessToken;
    }

    public function resolveAccessToken(): string
    {
        return $this->market_authentication_service->getClientCredentialsToken();
    }



}