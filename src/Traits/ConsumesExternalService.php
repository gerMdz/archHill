<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

trait ConsumesExternalService
{
    /**
     * @param $method
     * @param $requestUri
     * @param array $queryParams
     * @param array $formsParams
     * @param array $headers
     * @return mixed|string|stdClass
     * @throws GuzzleException
     */
    public function makeRequest($method, $requestUri, array $queryParams = [], array $formsParams = [], array $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        $bodyType = 'form_params';

//        dd($method, $requestUri, $queryParams, $formsParams, $headers);

        $response = $client->request($method, $requestUri, [
            'query' => $queryParams,
            $bodyType => $formsParams,
            'headers' => $headers,
        ]);

        $response = $response->getBody()->getContents();

        if (method_exists($this, 'decodeResponse')) {
            $response = $this->decodeResponse($response);
        }

        if (method_exists($this, 'checkIfErrorResponse')) {
            $this->checkIfErrorResponse($response);
        }

        return $response;
    }
}