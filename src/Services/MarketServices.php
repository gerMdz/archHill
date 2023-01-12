<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;
use Exception;
use stdClass;

class MarketServices
{
    use ConsumesExternalService;

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
     * @param array $responde
     * @return void
     * @throws Exception
     */
    public function CheckIfErrorResponse(array $responde): void
    {
        if(isset($responde->error)){
            throw new Exception("Fallo en la respuesta {$responde->error}" );
        }
    }

    /**
     * @param string $response
     * @return stdClass
     */
    public function decodeResponse(string $response): stdClass
    {
        $decodeResponse = json_decode($response);
        return $decodeResponse->data ?? $decodeResponse;
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

    /**
     * @return string
     */
    public function resolveAccessToken(): string
    {
        return 'Bearer '. $_ENV('BASE_TOKEN');
    }
}