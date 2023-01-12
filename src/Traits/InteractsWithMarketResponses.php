<?php

namespace App\Traits;

use Exception;
use stdClass;

trait InteractsWithMarketResponses
{
    /**
     * @param array $responde
     * @throws Exception
     */
    public function CheckIfErrorResponse(array $responde)
    {
        if(isset($responde->error)){
            throw new Exception("Fallo en la respuesta {$responde->error}" );
        }

        return $responde;
    }

    /**
     *
     */
    public function decodeResponse( $response)
    {

        $decodeResponse = json_decode($response);
        return $decodeResponse->data ?? $decodeResponse;
    }
}