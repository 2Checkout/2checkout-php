<?php

namespace Tco\Source\Api\Rest\V6;

use Tco\Interfaces\Auth;
use Tco\Source\TcoConfig;
use Tco\Exceptions\TcoException;


class ApiCore {

    /**
     * @var TcoConfig
     */
    public $tcoConfig;

    /**
     * @var Auth
     */
    public $auth;

    /**
     * ApiCore constructor.
     *
     * @param TcoConfig $tcoConfig
     * @param Auth $auth
     */
    public function __construct( $tcoConfig, $auth ) {
        $this->tcoConfig = $tcoConfig;
        $this->auth      = $auth;
    }

    /**
     * @param $endpoint
     * @param $params
     * @param string $method
     * @param string $apiLocation
     *
     * @return mixed
     * @throws \TcoException
     */
    public function call( $endpoint, $params, $method = 'POST', $apiLocation = 'restApi' ) {
        try {
            $url = $this->tcoConfig->getApiEndpoints()[ $apiLocation ] . $endpoint;
            $ch  = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->auth->getHeaders() );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_HEADER, false );

            if ( ! $this->tcoConfig->getCurlVerifySsl() ) {
                curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false ); //by default value is 2
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); //by default value is 1
            }

            if ( $method === 'POST' ) {
                curl_setopt( $ch, CURLOPT_POST, true );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $params, JSON_UNESCAPED_UNICODE ) );
            }
            $response = curl_exec( $ch );
            curl_close( $ch );
            if ( $response === false ) {
                throw new TcoException( sprintf('Curl response :%s', curl_error( $ch )));
            }

            return json_decode( $response, true );
        } catch ( Exception $e ) {
            throw new TcoException( sprintf('Exception ApiCore response: %s', $e->getMessage()
            ) );
        }
    }
}
