<?php

namespace Tco\Source\Api\Rest\V6;

use Tco\Exceptions\TcoException;

class Subscription {

    private $core;
    private $acceptedSearchSubscriptionParams = array(
        'SubscriptionReference',
        'CustomerEmail',
        'DeliveredCode',
        'AvangateCustomerReference',
        'ExternalCustomerReference',
        'Aggregate',
        'SubscriptionEnabled',
        'RecurringEnabled',
        'ProductCodes',
        'CountryCodes',
        'PurchasedAfter',
        'PurchasedBefore',
        'ExpireAfter',
        'ExpireBefore',
        'RenewedAfter',
        'RenewedBefore',
        'NotificationAfter',
        'NotificationBefore',
        'ModifiedAfter',
        'ModifiedBefore',
        'NextBillingDateAfter',
        'NextBillingDateBefore',
        'LifetimeSubscription',
        'ModifiedAfter',
        'MerchantCode'
    );

    public function __construct( $apiCore ) {
        $this->core = $apiCore;
    }

    public function validateSubscriptionSearchParams( $searchParams ) {
        $notAccepted = array();
        foreach ( $searchParams as $k => $v ) {
            if ( ! array_key_exists( $k, array_flip( $this->acceptedSearchSubscriptionParams ) ) ) {
                $notAccepted[] = $k;
            }
        }

        return $notAccepted;
    }

    /**
     * @returns array with keys (LineItemReference) && values (array of SubscriptionReferences)
     * or empty array
     */
    public function getSubscriptionsByOrderRefNo( $orderRefNo ) {
        $order     = new Order( $this->core );
        $orderData = null;
        try {
            $orderData = $order->getOrder( array( 'RefNo' => $orderRefNo ) );
        } catch ( TcoException $exception ) {
            throw new TcoException( sprintf( 'Exception getting subscriptions by order RefNo %s',
                $exception->getMessage() ) );
        }
        $items                   = $orderData["Items"];
        $subscriptionsRefNoArray = array();

        foreach ( $items as $k => $productData ) {
            if ( isset( $productData['ProductDetails']['Subscriptions'][0] ) ) {
                $productSubscriptions = $productData['ProductDetails']['Subscriptions'];
                $lineItemReference    = $items[ $k ]['LineItemReference'];
                foreach ( $productSubscriptions as $k => $subscription ) {
                    $subscriptionsRefNoArray[ $lineItemReference ][] = $subscription['SubscriptionReference'];
                }
            }
        }

        return $subscriptionsRefNoArray;

    }

    public function searchSubscriptions( $searchParams ) {
        $rejectedParams = $this->validateSubscriptionSearchParams( $searchParams );
        if ( count( $rejectedParams ) == 0 ) {
            try {
                $search = '/subscriptions/';
                //If subscriptionReferenceId then we only search by it
                if ( isset( $searchParams['SubscriptionReference'] ) ) {
                    $search .= $searchParams['SubscriptionReference'] . '/';
                    unset( $searchParams );
                }

                //Else do search for multiple orders using the mix of parameters.
                if ( ! empty( $searchParams ) ) {
                    $search .= '?' . http_build_query( $searchParams );
                }

                return $this->core->call( $search, [], 'GET' );
            } catch ( TcoException $exception ) {
                throw new TcoException( sprintf( 'Error when trying to search for subscription: %s',
                    $exception->getMessage() ) );
            }
        } else {
            throw new TcoException( sprintf( 'Exception! Some subscription search parameters are not accepted: %s',
                implode( ', ', $rejectedParams ) ) );
        }
    }

    public function updateSubscriptions( $updatedSubscriptionParams ) {
        if ( isset( $updatedSubscriptionParams['SubscriptionReference'] ) ) {
            $endPoint = '/subscriptions/' . $updatedSubscriptionParams['SubscriptionReference'] . '/';
            try {
                return $this->core->call( $endPoint, $updatedSubscriptionParams, 'PUT' );
            } catch ( TcoException $exception ) {
                throw new TcoException( sprintf( 'Exception when updating subscription! Message: %s',
                    $exception->getMessage() ) );
            }
        }
    }

    public function enableSubscriptions( $updatedSubscriptionParams ) {
        if ( isset( $updatedSubscriptionParams['SubscriptionReference'] ) ) {
            $endPoint = '/subscriptions/' . $updatedSubscriptionParams['SubscriptionReference'] . '/';
            try {
                return $this->core->call( $endPoint, [], 'POST' );
            } catch ( TcoException $exception ) {
                throw new TcoException( sprintf( 'Exception when Enabling subscription! Message: %s',
                    $exception->getMessage() ) );
            }
        }
    }

    public function disableSubscriptions( $updatedSubscriptionParams ) {
        if ( isset( $updatedSubscriptionParams['SubscriptionReference'] ) ) {
            $endPoint = '/subscriptions/' . $updatedSubscriptionParams['SubscriptionReference'] . '/';
            try {
                return $this->core->call( $endPoint, [], 'DELETE' );
            } catch ( TcoException $exception ) {
                throw new TcoException( sprintf( 'Exception when Enabling subscription! Message: %s',
                    $exception->getMessage() ) );
            }
        }
    }
}
