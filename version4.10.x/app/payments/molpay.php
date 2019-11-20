<?php

use Tygh\Http;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

// Return from molpay website
if (defined('PAYMENT_NOTIFICATION')) {
    
    $payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['orderid']);
    $processor_data = fn_get_payment_method_data($payment_id);
    $vkey = $processor_data['processor_params']['secret_key'];  
  
    if ($mode == 'return') {

        // ---------------- below don't change ------------------
        $tranID = $_POST['tranID'];
        $orderid = $_POST['orderid'];
        $status = $_POST['status'];
        $domain = $_POST['domain'];
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $appcode = $_POST['appcode'];
        $paydate = $_POST['paydate'];
        $skey = $_POST['skey'];

        //All undeclared variables below are coming from POST method
        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . $vkey);
        
        if ($skey != $key1)
            $status = -1; //invalid transaction

        if ($currency == "RM")
            $currency = "MYR";

        if ($status == "00") {
            //success transaction
            fn_change_order_status($orderid, 'P', '', false);
            fn_finish_payment($orderid, $_POST, false);
            fn_order_placement_routines('route', $orderid);
        } else if( $status == "22") {
            //for cash channel only
            fn_change_order_status($orderid, 'O', '', false);
            fn_finish_payment($orderid, $_POST, false);
            fn_order_placement_routines('route', $orderid, false);
        } else {
            //failed transaction
            fn_change_order_status($orderid, 'F', '', false);
            fn_finish_payment($orderid, $_POST, false);
            fn_order_placement_routines('route', $orderid, false);
        }


    } else if( $mode == "callback" ) {
        // ---------------- below don't change ------------------
        $tranID = $_POST['tranID'];
        $orderid = $_POST['orderid'];
        $status = $_POST['status'];
        $domain = $_POST['domain'];
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $appcode = $_POST['appcode'];
        $paydate = $_POST['paydate'];
        $skey = $_POST['skey'];

        //All undeclared variables below are coming from POST method
        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . $vkey);
        
        if ($skey != $key1)
            $status = -1; //invalid transaction

        if ($currency == "RM")
            $currency = "MYR";

        if ($status == "00") {
            //success transaction
            fn_change_order_status($orderid, 'P', '', false);
            fn_finish_payment($orderid, $_POST, false);
        } else if( $status == "22") {
            //for cash channel only
            fn_change_order_status($orderid, 'O', '', false);
            fn_finish_payment($orderid, $_POST, false);
        } else {
            //failed transaction
            fn_change_order_status($orderid, 'F', '', false);
            fn_finish_payment($orderid, $_POST, false);
        }
        if ($_POST['nbcb'] == 1){
            echo "CBTOKEN:MPSTATOK";
            exit;
        }

        exit;
    } else if( $mode == "notification" ) {
        //$vkey = $processor_data['processor_params']['verify_key'];
        // ---------------- below don't change ------------------
        $tranID = $_POST['tranID'];
        $orderid = $_POST['orderid'];
        $status = $_POST['status'];
        $domain = $_POST['domain'];
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $appcode = $_POST['appcode'];
        $paydate = $_POST['paydate'];
        $skey = $_POST['skey'];

        //All undeclared variables below are coming from POST method
        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . $vkey);
        
        if ($skey != $key1)
            $status = -1; //invalid transaction

        if ($currency == "RM")
            $currency = "MYR";

        if ($status == "00") {
            //success transaction
            fn_change_order_status($orderid, 'P', '', false);
            fn_finish_payment($orderid, $_POST, false);
        } else if( $status == "22") {
            //for cash channel only
            fn_change_order_status($orderid, 'O', '', false);
            fn_finish_payment($orderid, $_POST, false);
        } else {
            //failed transaction
            fn_change_order_status($orderid, 'F', '', false);
            fn_finish_payment($orderid, $_POST, false);
        }
        if ($_POST['nbcb'] == 2){
            exit;
        }
    }
    exit;
} else {
    $__bstate = $order_info['b_state'];
    if ($order_info['b_country'] != 'US' && $order_info['b_country'] != 'CA') {
        $__bstate = "XX";
    }
    $__sstate = @$order_info['s_state'];
    if ($order_info['s_country'] != 'US' && $order_info['s_country'] != 'CA') {
        $__sstate = "XX";
    }
    
    $orderid = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;

    $molpay_merchantID = $processor_data['processor_params']['merchant_id'];
    $molpay_verifykey = $processor_data['processor_params']['verify_key'];
    $molpay_secretkey = $processor_data['processor_params']['secret_key'];
    $molpay_url = $processor_data['processor_params']['mode'];

    $current_location = Registry::get('config.current_location');

    $molpay_currency = $processor_data['processor_params']['currency'];
    $molpay_channel = $processor_data['processor_params']['channel'];

    //Order Total
    $molpay_shipping = fn_order_shipping_cost($order_info);
    $molpay_total = fn_format_price($order_info['total'], $molpay_currency);

    $index_script = "index.php";

    $molpay_vcode = md5($molpay_total . $molpay_merchantID . $orderid . $molpay_verifykey);
    $base64 = base64_encode($molpay_vcode);

    $return_url = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . (substr($_SERVER['REQUEST_URI'], 0, 1) == '/' ? $_SERVER['REQUEST_URI'] : '/' . $_SERVER['REQUEST_URI']) . "?dispatch=payment_notification.return&payment=molpay&order_id=" . $orderid;

    $form_data = array(
        'status' => true,
        'startReq' => true,
        'merchantid' => $molpay_merchantID,
        'channel' => $_POST['payment_options'],
        'amount' => $molpay_total,
        'orderid' => $orderid,
        'bill_name' => $order_info['b_firstname'] . " " . $order_info['b_lastname'],
        'bill_email' => $order_info['email'],
        'bill_mobile' => $order_info['phone'],
        'country' => $order_info['b_country'],
        'vcode' => $molpay_vcode,
        'currency' => $molpay_currency,
        'dispatch' => "checkout.complete",
        'complete' => "Y",
        'mpsreturnurl' => $return_url,
        'molUrl' => $molpay_url,
    );

    if ($molpay_url == 'live'){
        $url = "https://www.onlinepayment.com.my/MOLPay/pay/" . $molpay_merchantID . "/";
    }else if($molpay_url == 'test'){
        $url = "https://sandbox.molpay.com/MOLPay/pay/" . $molpay_merchantID . "/";
    }

	$product_name="";
    if (!empty($order_info['products'])) {
        //if no description about the product, then create it
        foreach ($order_info['products'] as $k => $v) {
            $v['product'] = htmlspecialchars(strip_tags($v['product']));
            $v['price'] = fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount'], $molpay_currency);

            $product_name = $product_name . str_replace(', ', ' ', $v['product'] . " x " . $v['amount']) . "\n";
        }
        $form_data['mpsbill_desc'] = $product_name;
    }
	   	
	$form_data['mpscancelurl'] = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . (substr($_SERVER['REQUEST_URI'], 0, 1) == '/' ? $_SERVER['REQUEST_URI'] : '/' . $_SERVER['REQUEST_URI']) . "checkout/";

    fn_create_payment_form($url, $form_data, 'MOLPay', false);
	
}
exit;
?>
