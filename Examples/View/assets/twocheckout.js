window.addEventListener('load', function () {
    var jsPaymentClient = new TwoPayClient(credentials.sellerId),
        component = jsPaymentClient.components.create('card');
    component.mount('#card-element');

    var doAjaxRequest = function (fparams) {
        $.ajax({
            type: 'POST',
            url: fparams.formAction,
            data: {ess_token: fparams.token, testMode: fparams.isTest, useCore: fparams.useCore, refno: fparams.refno},
            dataType: 'json',
            xhrFields: { withCredentials: true }
        }).done(function (result) {
            console.log('Ajax done result: ',result);
            if (result.redirect) {
                window.location.href = result.redirect;
            } else if(result.success) {
                alert(result.msg);
            }
            else{
                console.log(result.error);
            }
        }).fail(function (response) {
            alert('Your payment could not be processed. Please refresh the page and try again!');
            console.log(response);
        });
    };

    var getFormParamsObj = function (formId) {
        return {
            formAction: $('#'+formId).attr('action'),
            isTest: document.getElementById("testPayment").checked,
            useCore: document.getElementById("useCore").checked,
            refno: ''
        }
    }

    //for placing orders with 2pay.js
    $('body').on('click', '#submitPayment', function (e) {
        console.log('Clicked on submitPayment!');
        e.preventDefault();
        var billingDetails = {name: document.querySelector('#name').value};
        var params = getFormParamsObj('payment-form');
        jsPaymentClient.tokens.generate(component, billingDetails).then(function (response) {
            params.token = response.token;
            doAjaxRequest(params);
        }).catch(function (error) {
            alert(error);
            console.log(error);
        });
    });

    //for placing orders with buy links
    $('body').on('click', '#submitBuyLinkRequest', function (e) {
        e.preventDefault();
        var params = getFormParamsObj('buylink-form');
        doAjaxRequest(params);
    });

    //for searching for order subscriptions
    $('body').on('click', '#submitOrderWithSubscription', function (e) {
        console.log('Clicked on Subscription!');
        e.preventDefault();
        var params = getFormParamsObj('subscrSrch-form');
        params.refno = $('#refno').val();
        doAjaxRequest(params);
    });

});
