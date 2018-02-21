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
    $vkey = $processor_data['processor_params']['verify_key'];  
  
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
    $is_test = ($processor_data['processor_params']['mode'] == 'test') ? 'Y' : 'N';
    
    //enable/disable status
    //$statusmode = ($processor_data['processor_params']['status']) == 'enable') ? 'Y' : 'N';
    
    $orderid = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;

    $molpay_merchantID = $processor_data['processor_params']['merchant_id'];
    $molpay_verifykey = $processor_data['processor_params']['verify_key'];
    $molpay_secretkey = $processor_data['processor_params']['secret_key'];

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
        'mpsmerchantid' => $molpay_merchantID,
        'mpschannel' => $_POST['payment_options'],
        'mpsamount' => $molpay_total,
        'mpsorderid' => $orderid,
        'mpsbill_name' => $order_info['b_firstname'] . " " . $order_info['b_lastname'],
        'mpsbill_email' => $order_info['email'],
        'mpsbill_mobile' => $order_info['phone'],
        'mpscountry' => $order_info['b_country'],
        'mpsvcode' => $molpay_vcode,
        'mpscurrency' => $molpay_currency,
        'dispatch' => "checkout.complete",
        'complete' => "Y",
        'mpsreturnurl' => $return_url,
    );

    if (!empty($order_info['products'])) {
        //if no description about the product, then create it
        foreach ($order_info['products'] as $k => $v) {
            $v['product'] = htmlspecialchars(strip_tags($v['product']));
            $v['price'] = fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount'], $molpay_currency);

            $product_name = $product_name . str_replace(', ', ' ', $v['product'] . " x " . $v['amount']) . "\n";
        }
        $form_data['mpsbill_desc'] = $product_name;
    }
    

    //$form_data['mpscancelurl'] = "http://{yourdomainame}/checkout/";
    $form_data['mpscancelurl'] = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . (substr($_SERVER['REQUEST_URI'], 0, 1) == '/' ? $_SERVER['REQUEST_URI'] : '/' . $_SERVER['REQUEST_URI']) . "checkout/";		
    header('Content-Type: application/json');
    echo json_encode($form_data);
}
exit;
?>
