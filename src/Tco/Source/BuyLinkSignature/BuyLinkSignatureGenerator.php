<?php

namespace Tco\Source\BuyLinkSignature;

use Tco\Exceptions\TcoException;
use Tco\Source\Api\Rest\V6\ApiCore;
use Tco\Source\TcoConfig;
use Tco\Source\Api\Auth\AuthFactory;
use Tco\Source\BuyLinkSignature\Jwt\JwtGenerator;

class BuyLinkSignatureGenerator {

    /**
     * @var TcoConfig
     */
    private $tcoConfig;

    /**
     * BuyLinkSignatureGenerator constructor.
     *
     * @param TcoConfig $tcoConfig
     */
    public function __construct( $tcoConfig ) {
        $this->tcoConfig = $tcoConfig;
    }

    /**
     * @param array $params
     *
     * @return string|null
     * @throws \TcoException
     */
    public function generateSignature( $params ) {
        try {
            $auth = ( new AuthFactory( $this->tcoConfig ) )->getAuth( 'BuyLink' );
            //Generate the token now
            $jwtGenerator = new JwtGenerator( $this->tcoConfig );
            $token        = $jwtGenerator->generateToken();
            $auth->setMerchantToken( $token );

            $api      = new ApiCore( $this->tcoConfig, $auth );
            $response = $this->doApiRequest( $api, $params );
            if ( isset( $response['signature'] ) ) {
                return $response['signature'];
            } else {
                throw new TcoException( sprintf('Api response is missing Signature parameter: ,%s', print_r($response,
                    true)) );
            }
        } catch ( TcoException $exception ) {
            throw new TcoException(  sprintf( 'Exception generating buy link signature: %s', $exception->getMessage() ) );
        }
    }

    /**
     * @param ApiCore $api
     * @param array $params
     *
     * @return array
     * @throws \TcoException
     */
    private function doApiRequest( $api, $params ) {
        try {
            return $api->call( '', $params, 'POST', 'buyLink' );
        }
        catch (TcoException $exception){
            throw new TcoException($exception->getMessage());
        }

    }

}
