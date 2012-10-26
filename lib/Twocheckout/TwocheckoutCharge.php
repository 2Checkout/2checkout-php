<?php

class Twocheckout_Charge extends Twocheckout
{

    public static function form($params, $type="Checkout")
    {
        echo "<form id=\"2checkout\" action=\"https://www.2checkout.com/checkout/spurchase\" method=\"post\">\n";

        foreach ($params as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"$key\" value=\"$value\"/>\n";
        }
        if ($type == "auto") {
            echo "<input type=\"submit\" value=\"Click here if you are not redirected automatically\" /></form>\n";
            echo "<script type=\"text/javascript\">document.getElementById('2checkout').submit();</script>";
        } else {
            echo "<input type=\"submit\" value=\"$type\" />\n";
            echo "</form>";
        }
    }

    public static function link($params)
    {
        $url = "https://www.2checkout.com/checkout/spurchase?".http_build_query($params, '', '&amp;');
        return $url;
    }

    public static function redirect($params)
    {
        $url = "https://www.2checkout.com/checkout/spurchase?".http_build_query($params, '', '&amp;');
        header("Location: $url");
    }

}