<?php

namespace App\Traits;

trait AuthorizesMarketRequests
{
    /**
     * @param $queryParams
     * @param $formsParams
     * @param $headers
     * @return void
     */
    public function resolveAuthorization(&$queryParams, &$formsParams, &$headers)
    {


        $accessToken = $this->resolveAccessToken();

        $headers['Authorization'] = $accessToken;
    }

    /**
     * @return string
     */
    public function resolveAccessToken(): string
    {
        return 'Bearer '. $this->base_token;
    }
}