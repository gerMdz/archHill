<?php

namespace App\Traits;

use Exception;
use stdClass;

trait InteractsWithMarketResponses
{
    /**

     * @return array
     * @throws Exception
     */
    public function CheckIfErrorResponse($responde)
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