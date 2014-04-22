<?php
    use Tygh\Http;
    use Tygh\Registry;

    if ( !defined('AREA') ) { die('Access denied'); }

    // Return from molpay website
    if (defined('PAYMENT_NOTIFICATION')) {
        if ($mode == 'return') {

            $vkey = base64_decode($_GET['key']);
            //------ below don't change ---------------
            $tranID =$_POST['tranID'];
            $orderid =$_REQUEST['order_id'];////$_POST['orderid'];
            $status =$_POST['status'];
            $domain =$_POST['domain'];
            $amount =$_POST['amount'];
            $currency =$_POST['currency'];
            $appcode =$_POST['appcode'];
            $paydate =$_POST['paydate'];
            $skey =$_POST['skey'];
            // All undeclared variables below are coming from POST method
            $key0 = md5( $tranID.$orderid.$status.$domain.$amount.$currency );
            $key1 = md5( $paydate.$domain.$key0.$appcode.$vkey );

            if( $skey != $key1 ) $status= -1; // invalid transaction

            if ($currency == "RM") $currency = "MYR";

            if ( $status == "00" ) 
            {
                //success
                //$orderid=130;//testing
                fn_change_order_status($orderid, 'P', '', false);
                fn_finish_payment($orderid, $_POST, false);
                fn_order_placement_routines('route',$orderid, false);
            } else {
                //failed
                fn_change_order_status($orderid, 'F', '', false);
                fn_finish_payment($orderid, $_POST, false);
                fn_order_placement_routines('route',$orderid, false);
            }


            //$status =$_POST['status'];
            //exit;	
        }
    } else {

        $molpay_merchantID = $processor_data['processor_params']['merchantID'];
        $molpay_verifykey = $processor_data['processor_params']['verifykey'];

        $current_location = Registry::get('config.current_location');

        $molpay_url = "https://www.onlinepayment.com.my/MOLPay/pay/".$molpay_merchantID."/";

        $molpay_currency = $processor_data['processor_params']['currency'];//edit

        //Order Total
        $molpay_shipping = fn_order_shipping_cost($order_info);
        //$molpay_shipping = fn_format_price($molpay_shipping, $molpay_currency);
        $molpay_total = fn_format_price($order_info['total'], $molpay_currency);

        $index_script="index.php";//edit

        $molpay_vcode = md5($molpay_total.$molpay_merchantID.$order_id.$molpay_verifykey);
        $base64 = base64_encode($molpay_verifykey);

        $msg = fn_get_lang_var('text_cc_processor_connection');
        $msg = str_replace('[processor]', 'MOLPay', $msg);
        echo <<<EOT
        <html>
        <body onLoad="document.molpay_form.submit();">
        <form action="{$molpay_url}" method="post" name="molpay_form">
        <input type=hidden name="orderid" value="$order_id">
        <input type=hidden name="bill_email" value="{$order_info['email']}">
        <input type=hidden name="bill_mobile" value="{$order_info['phone']}">
EOT;
        // Products
        if (!empty($order_info['products'])) {///IF NO HAVE DESRIPTION CREATE THE DESCRIPTION
            foreach ($order_info['products'] as $k => $v) {
                    $v['product'] = htmlspecialchars(strip_tags($v['product']));
                    $v['price'] = fn_format_price(($v['subtotal'] - fn_external_discounts($v)) / $v['amount'], $molpay_currency);	

                    $product_name = $product_name . str_replace(', ', ' ', $v['product']." x ".$v['amount']) . "\n";
            }
        }
        //index.php?dispatch=payment_notification.return&payment=paypal&order_id=115
        $success_url = fn_url("payment_notification.return&payment=molpay&order_id={$order_id}&key=$base64", AREA, 'current');	
        //$allowed_id = db_get_field("update user_id FROM ?:orders WHERE user_id = ?i AND order_id = ?i", 1, 121);
        //echo "ggg".$allowed_id;

        //$success_url = fn_url("checkout.complete&payment=molpay&order_id={$order_id}", AREA, 'current');

        echo <<<EOT
        <input type="hidden" name="bill_desc" value="{$product_name}" />
        <input type=hidden name="bill_name" value="{$order_info['b_firstname']} {$order_info['b_lastname']}">
        <input type=hidden name="amount" value="{$molpay_total}">
        <input type=hidden name="cur" value="{$processor_data['processor_params']['currency']}">
        <input type=hidden name="vcode" value="{$molpay_vcode}">
        <input type=hidden name="country" value="{$order_info['b_country']}">
        <input type="hidden" name="order_id" value="$order_id" />
        <input type="hidden" name="dispatch" value="checkout.complete" />
        <input type="hidden" name="complete" value="Y" />
        <input type="hidden" name="payment" value="molpay" />
        <input type="hidden" name="returnurl" value="$success_url">
        <input type="hidden" name="status" value="O" />

        </form>
        <div align=center>{$msg}</div>
        </body>
        </html>
EOT;
        // Cart is empty, create it
        //if (empty($_SESSION['cart'])) {
                //fn_clear_cart($_SESSION['cart']);///clear chart
        //}
        //$cart = & $_SESSION['cart'];

        ///update table///
        //echo $update= db_query("UPDATE ?:orders SET status = ?s WHERE order_id = ?i", 'O', $order_id);

        fn_flush();
    }
    exit;
?>