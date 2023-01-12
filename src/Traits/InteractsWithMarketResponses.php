<?php

namespace App\Traits;

use Exception;
use stdClass;

trait InteractsWithMarketResponses
{
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
}