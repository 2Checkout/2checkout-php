2Checkout PHP Library
=====================

This library provides developers with a simple set of bindings to the 2Checkout purchase routine, Instant Notification Service and Back Office API.

To use, download or clone the repository.

```shell
git clone https://github.com/2checkout/2checkout-php.git
```

Require in your php script.

```php
require_once("/path/to/2checkout-php/lib/Twocheckout.php");
```

JSON is returned by default or you can add 'array' as an additional argument to each call to get an Array.
**Example:**
```php
<?php
Twocheckout_Sale::refund($args, 'array');
```

Full documentation for each binding is provided in the [Wiki](https://github.com/2checkout/2checkout-php/wiki).


Example API Usage
-----------------

*Example Request:*

```php
<?php
Twocheckout::setCredentials("APIuser1817037", "APIpass1817037");
$args = array('sale_id' => 4834917619);
Twocheckout_Sale::stop($args, 'array');
```

*Example Response:*

```php
<?php

[response_code] => OK
[response_message] => Array
    (
        [0] => 4834917634
        [1] => 4834917646
        [2] => 4834917658
    )
```

Example Checkout Usage:
-----------------------

*Example Request:*

```php
<?php
$params = array(
    'sid' => '1817037',
    'mode' => '2CO',
    'li_0_name' => 'Test Product',
    'li_0_price' => '0.01'
);
Twocheckout_Charge::form($params, 'auto');
```

*Example Response:*
```php
<form id="2checkout" action="https://www.2checkout.com/checkout/spurchase" method="post">
<input type="hidden" name="sid" value="1817037"/>
<input type="hidden" name="mode" value="2CO"/>
<input type="hidden" name="li_0_name" value="Test Product"/>
<input type="hidden" name="li_0_price" value="0.01"/>
<input type="submit" value="Click here if you are not redirected automatically" /></form>
<script type="text/javascript">document.getElementById('2checkout').submit();</script>
```

Example Return Usage:
---------------------

*Example Request:*

```php
<?php
$params = array();
foreach ($_REQUEST as $k => $v) {
    $params[$k] = $v;
}
$passback = Twocheckout_Return::check($params, "tango", 'array');
```

*Example Response:*

```php
<?php

[response_code] => Success
[response_message] => Hash Matched
```

Example INS Usage:
------------------

*Example Request:*

```php
<?php
$params = array();
foreach ($_POST as $k => $v) {
    $params[$k] = $v;
}
$passback = Twocheckout_Notification::check($params, "tango", 'array');
```

*Example Response:*

```php
<?php

[response_code] => Success
[response_message] => Hash Matched
```

Exceptions:
-----------
Twocheckout_Error exceptions are thrown by if an error has returned. It is best to catch these exceptions so that they can be gracefully handled in your application.

*Example Usage*

```php
<?php

Twocheckout::setCredentials("APIuser1817037", "APIpass1817037");

$params = array(
'sale_id' => 4774380224,
'category' => 1,
'comment' => 'Order never sent.'
);
try {
  $sale = Twocheckout_Sale::refund($params, 'array');
} catch (Twocheckout_Error $e) {
  $e->getMessage());
}
```

Full documentation for each binding is provided in the [Wiki](https://github.com/2checkout/2checkout-php/wiki).
