<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

trait ConsumesExternalService
{
    /**
     * @param $method
     * @param $requestUri
     * @param array $queryParams
     * @param array $formsParams
     * @param array $headers
     * @return string
     * @throws GuzzleException
     */
    public function MakeRequest($method, $requestUri, array $queryParams = [], array $formsParams = [], array $headers = []): string
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $formsParams, $headers);
        }

        $response =  $client->request($method, $requestUri, [
            'query' => $queryParams,
            'forms_param' => $formsParams,
            'headers' => $headers

        ]);

        $response = $response->getBody()->getContents();

        if (method_exists($this, 'decodeResponse')) {
            $response = $this->decodeResponse($response);
        }

        if (method_exists($this, 'CheckIfErrorResponse')) {
            $response = $this->CheckIfErrorResponse($response);
        }

        return $response;


    }
}