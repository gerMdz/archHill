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
    public function resolveAuthorization(&$queryParams, &$formsParams, &$headers): void
    {
        $accessToken = $this->resolveAccessToken();

        $headers['Authorization'] = $accessToken;
    }

    /**
     * @return string
     */
    public function resolveAccessToken(): string
    {
        return 'Bearer '. $_ENV('BASE_TOKEN');
    }
}