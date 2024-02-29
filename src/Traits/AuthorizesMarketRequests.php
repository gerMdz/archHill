<?php

namespace App\Traits;

use App\Services\MarketAuthenticationService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\RequestStack;


trait AuthorizesMarketRequests
{


    protected MarketAuthenticationService $market_authentication_service;
    private RequestStack $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


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
     * @throws GuzzleException
     */
    public function resolveAuthorization(&$queryParams, &$formsParams, &$headers): void
    {
        $accessToken = $this->resolveAccessToken();
        $headers['Authorization'] = $accessToken;
    }

    /**
     * @throws GuzzleException
     */
    public function resolveAccessToken(): string
    {
        if(isset($this->requestStack) && $this->requestStack->getCurrentRequest()->getUser()) {
            return $this->market_authentication_service->getAuthenticatedUserToken();
        }

        return $this->market_authentication_service->getClientCredentialsToken();
    }


}