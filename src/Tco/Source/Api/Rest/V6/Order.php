<?php
namespace Tco\Source\Api\Rest\V6;

class Order{

    /**
     * @var ApiCore
     */
    private $core;

    /**
     * @var array
     */
    private $acceptedSearchOrderParams = array(
        'RefNo',
        'ApproveStatus',
        'Newer',
        'Status',
        'StartDate',
        'EndDate',
        'PartnerOrders',
        'ExternalRefNo',
        'Page',
        'Limit'
    );

    /**
     * Order constructor.
     *
     * @param ApiCore $apiCore
     */
    public function __construct( $apiCore ) {
        $this->core = $apiCore;
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws \TcoException
     */
    public function place( $params) {
        try {
            return $this->core->call( '/orders/', $params );
        } catch ( TcoException $exception ) {
            throw new TcoException( sprintf( 'Error when trying to place order: %s', $exception->getMessage() ) );
        }
    }

    /**
     * @param array $searchParams
     *
     * @return array
     * @throws \TcoException
     */
    public function getOrder( $searchParams) {
        $rejectedParams = $this->validateOrderSearchParams( $searchParams );
        if ( count( $rejectedParams ) == 0 ) {
            try {
                $search = '/orders/';

                //If RefNo then we only search by it
                if ( isset( $searchParams['RefNo'] ) ) {
                    $search .= $searchParams['RefNo'] . '/';
                    unset( $searchParams );
                }
                //Else do search for multiple orders using the mix of parameters.
                if ( ! empty( $searchParams ) ) {
                    $search .= '?' . http_build_query( $searchParams );
                }
                return $this->core->call( $search, [], 'GET' );

            } catch ( TcoException $exception ) {
                throw new TcoException( sprintf( 'Error when trying to search for order: %s', $exception->getMessage() ) );
            }
        } else {
            throw new TcoException( sprintf( 'Exception! Some order search parameters are not accepted: %s', implode(
                ', ',
                $rejectedParams ) ) );
        }
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws \TcoException
     */
    public function issueRefund( $params ) {
        if ( ! isset( $params['RefNo'] ) ) {
            throw new TcoException( 'RefNo is required for Refund!' );
        }
        $refNo = $params['RefNo'];
        if ( ! isset( $params['refundParams'] ) ) {
            throw new TcoException( 'Refund parameters are required for Refund!' );
        }
        $refundParams = $params['refundParams'];

        try {
            return $this->core->call( 'orders/' . $refNo . '/refund/', $refundParams, 'POST' );
        } catch ( TcoException $exception ) {
            throw new TcoException( sprintf( 'Error when placing refund request: %s', $exception->getMessage() ) );
        }
    }

    /**
     * @param $searchParams
     *
     * @return array
     */
    public function validateOrderSearchParams( $searchParams ) {
        $notAccepted = array();
        foreach ( $searchParams as $k => $v ) {
            if ( ! array_key_exists( $k, array_flip( $this->acceptedSearchOrderParams ) ) ) {
                $notAccepted[] = $k;
            }
        }
        return $notAccepted;
    }
}
