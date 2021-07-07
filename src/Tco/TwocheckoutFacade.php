<?php

namespace Tco;

use Tco\Exceptions\TcoException;
use Tco\Source\Api\Rest\V6\ApiCore;
use Tco\Source\Api\Rest\V6\Order;
use Tco\Source\Api\Rest\V6\Subscription;
use Tco\Source\Api\Auth\AuthFactory;
use Tco\Source\Ipn\IpnSignature;
use Tco\Source\TcoConfig;
use Tco\Source\BuyLinkSignature\BuyLinkSignatureGenerator;

class TwocheckoutFacade {

    private $tcoConfig;
    private $authApi;
    private $apiCore;


    /**
     * TwocheckoutFacade constructor.
     *
     * @param array $config
     *
     * @throws \TcoException
     */
    public function __construct( $config ) {
        try {
            $this->tcoConfig = new TcoConfig( $config );
            $this->authApi   = ( new AuthFactory( $this->tcoConfig ) )->getAuth();
            $this->apiCore   = new ApiCore( $this->tcoConfig, $this->authApi );
        } catch ( TcoException $exception ) {
            throw new TcoException( $exception->getMessage() );
        }
    }

    /**
     * @return ApiCore
     */
    public function apiCore() {
        return $this->apiCore;
    }

    /**
     * @return Order
     */
    public function order() {
        return new Order( $this->apiCore );
    }

    /**
     * @return Subscription
     */
    public function subscription() {
        return new Subscription( $this->apiCore );
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getBuyLinkSignature( $params ) {
        try {
            $generator = new BuyLinkSignatureGenerator( $this->tcoConfig );

            return $generator->generateSignature( $params );

        } catch ( TcoException $e ) {
            throw new TcoException( sprintf( 'Exception getting buy link signature! Details: %s', $e->getMessage() ) );
        }
    }

    public function validateIpnResponse( $ipnParams ) {
        try {
            $ipnSignature = new IpnSignature( $this->tcoConfig );

            return $ipnSignature->isIpnValid( $ipnParams );

        } catch ( TcoException $e ) {
            throw new TcoException( sprintf( 'Cannot validate Ipn Request. Details: %s', $e->getMessage() ) );
        }
    }

    public function generateIpnResponse( $ipnParams ) {
        try {
            $ipnSignature = new IpnSignature( $this->tcoConfig );

            return $ipnSignature->calculateIpnResponse( $ipnParams );
        } catch ( TcoException $e ) {
            throw new TcoException( sprintf( 'Cannot generate Ipn Response. Details: %s', $e->getMessage() ) );
        }
    }
}
