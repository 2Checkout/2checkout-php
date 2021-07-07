<?php

require_once __DIR__ . '/../autoloader.php';

use Tco\Examples\Common\OrderParams\DynamicProducts;
use Tco\Examples\Common;

$orderParams = new DynamicProducts();
$config = new Common\ExampleConfig();

$dynamicProductParamsSuccess = $orderParams->getDynamicProductSuccessParams();
$credentials                 = array('sellerId'=>$config->sellerId());
$billingDetails              = $dynamicProductParamsSuccess['BillingDetails'];
$orderTotal                  = Common\Helpers\MyHelper::calculateTotal( $dynamicProductParamsSuccess );

$placedOrder = ( isset( $_GET['success'] ) && isset( $_GET['refno'] ) ) ? $_GET['refno'] : null;
$paymentCallbackMsg = ( isset( $_GET['success'] ) && isset( $_GET['msg'] ) ) ? $_GET['msg'] : null;
$paymentFailed = isset ( $_GET['error']) ?  $_GET['error'] : null;

?>
<html>
<head>
    <title>
        Payment Form Example
    </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
          integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="view/assets/style.css">
    <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns"
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://2pay-js.2checkout.com/v1/2pay.js"></script>
    <script src="view/assets/scripts.js"></script>
    <script>
        var credentials = <?php echo json_encode( $credentials ) ?>;
        var billingDetails = <?php echo json_encode( $billingDetails ) ?>;
    </script>
    <script src="view/assets/twocheckout.js"></script>
</head>
<body oncontextmenu='return false' class='snippet-body'>
<div class="container-fluid px-0" id="bg-div">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-12">
            <div class="card card0">
                <div class="d-flex" id="wrapper">
                    <!-- Sidebar -->
                    <div class="bg-light border-right" id="sidebar-wrapper">
                        <div class="sidebar-heading pt-5 pb-4"><strong>Library Examples</strong></div>
                        <div class="list-group list-group-flush">
                            <a data-toggle="tab" href="#menu1" id="tab1"
                                                                    class="tabs list-group-item active1">
                                <div class="list-div my-2">
                                    <div class="fa fa-credit-card"></div> &nbsp;&nbsp;Pay & Place Order<span
                                            id="new-label">Api</span>
                                </div>
                            </a>
                            <a data-toggle="tab" href="#menu2" id="tab2" class="tabs list-group-item bg-light">
                                <div class="list-div my-2">
                                    <div class="fa fa-qrcode"></div> &nbsp;&nbsp;&nbsp; Generate Buy Link <span
                                            id="new-label">Api</span>
                                </div>
                            </a>
                            <a data-toggle="tab" href="#menu3" id="tab3" class="tabs list-group-item bg-light">
                                <div class="list-div my-2">
                                    <div class="fa fa-qrcode"></div> &nbsp;&nbsp;&nbsp; Subscriptions <span
                                            id="new-label">Api</span>
                                </div>
                            </a>
                        </div>
                    </div> <!-- Page Content -->
                    <div id="page-content-wrapper">
                        <?php if ( $placedOrder ) { ?>
                            <div class="row pt-3">
                                <div class="col-12">
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $paymentCallbackMsg; ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ( $paymentFailed ) { ?>
                            <div class="row pt-3">
                                <div class="col-12">
                                    <div class="alert alert-danger" role="alert">
                                        Payment Error - <?php echo $paymentFailed; ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row pt-3" id="border-btm">

                            <div class="col-4">
                                <button class="btn btn-success mt-4 ml-3 mb-3" id="menu-toggle">
                                    <div class="bar4"></div>
                                    <div class="bar4"></div>
                                    <div class="bar4"></div>
                                </button>
                            </div>
                            <div class="col-8">
                                <div class="row justify-content-right">
                                    <div class="col-12">
                                        <p class="mb-0 mr-4 mt-4 text-right"><?php echo $billingDetails['Email'];
                                            ?></p>
                                    </div>
                                </div>
                                <div class="row justify-content-right">
                                    <div class="col-12">
                                        <p class="mb-0 mr-4 text-right">Pay <span class="top-highlight"> <?php echo
                                                    $dynamicProductParamsSuccess['Currency'] . ' ' . $orderTotal;
                                                ?></span></p>
                                    </div>
                                </div>
                                <div class="row justify-content-right">
                                    <div class="col-12">
                                        <div class="form-check form-switch mb-0 mr-4 text-right">
                                            <input class="form-check-input" type="checkbox" id="testPayment"
                                                   checked>
                                            <label class="form-check-label" for="testPayment">TEST
                                                Payment</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-right">
                                    <div class="col-12">
                                        <div class="form-check form-switch mb-0 mr-4 text-right">
                                            <input class="form-check-input" type="checkbox" id="useCore">
                                            <label class="form-check-label" for="useCore">Use Api Core</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="text-center" id="test">Twocheckout PHP Library by Verifone</div>
                        </div>
                        <div class="tab-content">
                            <div id="menu1" class="tab-pane in active">
                                <div class="row justify-content-center">
                                    <div class="col-11">
                                        <div class="form-card">
                                            <h3 class="mt-0 mb-4 text-center">Enter your card details to pay</h3>
                                            <form type="post" id="payment-form"
                                                  action="Controllers/Order/generateApiRequest.php">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="input-group"><input type="text" id="name"
                                                                                        placeholder="John Doe"
                                                                                        minlength="3" maxlength="50">
                                                            <label>Name</label></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12" id="card-element">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12"><input type="submit" id="submitPayment"
                                                                                  value="Generate Token and Pay"
                                                                                  class="btn btn-primary placeicon">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="menu2" class="tab-pane">
                                <div class="row justify-content-center">
                                    <div class="col-11">
                                        <div class="form-card">
                                            <h3 class="mt-0 mb-4 text-center">Buy Link & Signature Generation
                                                Example</h3>
                                            <h5 class="mt-0 mb-4 text-center">Order with Subscription</h5>
                                            <form type="post" id="buylink-form"
                                                  action="Controllers/BuyLink/generateBuyLinkSignature.php">
                                                <div class="row">
                                                    <div class="col-2" ></div>
                                                    <div class="col-md-8"><input type="submit"
                                                                                  id="submitBuyLinkRequest"
                                                                                  value="Generate Buy Link Signature"
                                                                                  class="btn btn-primary placeicon">
                                                    </div>
                                                    <div class="col-2" ></div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="menu3" class="tab-pane">
                                <div class="row justify-content-center">
                                    <div class="col-11">
                                        <div class="form-card">
                                            <h3 class="mt-0 mb-4 text-center">Subscription
                                                Examples</h3>
                                            <form type="post" id="subscrSrch-form"
                                                  action="Controllers/Subscription/generateApiRequest.php">
                                                <input type="hidden" value="<?php echo $placedOrder; ?>" id="refno">
                                                <div class="row">
                                                    <div class="col-3" ></div>
                                                    <div class="col-md-6"><input type="submit"
                                                                                 id="submitOrderWithSubscription"
                                                                                 value="Search By BuyLink Order Id"
                                                                                 class="btn btn-primary placeicon">
                                                    </div>
                                                    <div class="col-3" ></div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
