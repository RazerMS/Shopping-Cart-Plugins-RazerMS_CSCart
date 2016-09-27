<?php

use Tygh\Http;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
// Return from molpay website
if (defined('PAYMENT_NOTIFICATION')) {
    if ($mode == 'return') {
        $payment_id = db_get_field("SELECT payment_id FROM ?:orders WHERE order_id = ?i", $_REQUEST['orderid']);
        $processor_data = fn_get_payment_method_data($payment_id);
        $vkey = $processor_data['processor_params']['verify_key'];
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
            fn_change_order_status($orderid, 'P', '');
            fn_finish_payment($orderid, $_POST);
            fn_order_placement_routines('route', $orderid);
        } else {
            //failed transaction
            fn_change_order_status($orderid, 'F', '');
            fn_finish_payment($orderid, $_POST);
            fn_order_placement_routines('route', $orderid);
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
    $orderid = ($order_info['repaid']) ? ($order_id . '_' . $order_info['repaid']) : $order_id;

    $molpay_merchantID = $processor_data['processor_params']['merchant_id'];
    $molpay_verifykey = $processor_data['processor_params']['verify_key'];

    $current_location = Registry::get('config.current_location');

    $molpay_url = "https://www.onlinepayment.com.my/MOLPay/pay/" . $molpay_merchantID . "/";

    $molpay_currency = $processor_data['processor_params']['currency'];

    //Order Total
    $molpay_shipping = fn_order_shipping_cost($order_info);
    $molpay_total = fn_format_price($order_info['total'], $molpay_currency);

    $index_script = "index.php";

    $molpay_vcode = md5($molpay_total . $molpay_merchantID . $orderid . $molpay_verifykey);
    $base64 = base64_encode($molpay_vcode);

    $return_url = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . (substr($_SERVER['REQUEST_URI'], 0, 1) == '/' ? $_SERVER['REQUEST_URI'] : '/' . $_SERVER['REQUEST_URI']) . "index.php?dispatch=payment_notification.return&payment=molpay&order_id=" . $orderid;


    $form_data = array(
        'orderid' => $orderid,
        'bill_email' => $order_info['email'],
        'bill_mobile' => $order_info['phone'],
        'bill_name' => $order_info['b_firstname'] . " " . $order_info['b_lastname'],
        'amount' => $molpay_total,
        'cur' => $processor_data['processor_params']['currency'],
        'vcode' => $molpay_vcode,
        'country' => $order_info['b_country'],
        'order_id' => $orderid,
        'dispatch' => "checkout.complete",
        'complete' => "Y",
        'returnurl' => $return_url,
        'payment' => "molpay",
        'status' => 0
    );

    if (!empty($order_info['products'])) {
        //if no description about the product, then create it
        foreach ($order_info['products'] as $k => $v) {
            $v['product'] = htmlspecialchars(strip_tags($v['product']));
            $v['price'] = fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount'], $molpay_currency);

            $product_name = $product_name . str_replace(', ', ' ', $v['product'] . " x " . $v['amount']) . "\n";
        }
        $form_data = fn_array_merge($form_data, array('bill_desc' => $product_name));
    }
    $pos = strpos($_SERVER['REQUEST_URI'], $index_script);
    if ($pos == true)
        $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $pos);

    fn_create_payment_form($molpay_url, $form_data, 'MOLPay', false);
}
exit;
?>
