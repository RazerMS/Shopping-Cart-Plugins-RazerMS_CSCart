<?php

use Tygh\Http;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($mode == "setup") {
    $data = array(
        "processor" => "MOLPay Malaysia Online Payment",
        "processor_script" => "molpay.php",
        "processor_template" => "views/orders/components/payments/cc_outside.tpl",
        "admin_template" => "molpay.tpl",
        "callback" => "N",
        "type" => "P",
        "addon" => ""
    );

    $sql = db_query("SELECT processor_id FROM ?:payment_processors WHERE processor = ?s", $data['processor']);
    $chk = mysqli_fetch_array($sql);

    if (!$chk[0]) {
        $result = db_query("INSERT INTO ?:payment_processors ?e", $data);
        
        //Mapping parameter response from MOLPay ( to remove underscore symbol at parameter key )
        $params = array(
                    array(
                        "lang_code"     => "en",
                        "name"          => "nbcb",
                        "value"         => "nbcb"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "tranid",
                        "value"         => "Transaction ID"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "status",
                        "value"         => "Status"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "orderid",
                        "value"         => "Order id"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "error_desc",
                        "value"         => "Error detail"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "error_code",
                        "value"         => "Error code"
                    ),
                   array(
                        "lang_code"     => "en",
                        "name"          => "domain",
                        "value"         => "Domain"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "amount",
                        "value"         => "Amount"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "currency",
                        "value"         => "Currency"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "appcode",
                        "value"         => "App code"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "paydate",
                        "value"         => "Payment Date"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "skey",
                        "value"         => "Secret Key"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "channel",
                        "value"         => "Channel"
                    ),
                    array(
                        "lang_code"     => "en",
                        "name"          => "extrap",
                        "value"         => "extraP"
                    ),

                  );
		
		$i=0;
		foreach($params as $k => $v){
			$sql = db_query("SELECT name, value FROM ?:language_values WHERE name = ?s", $v['name']);
			$chk = mysqli_fetch_array($sql);
			if (!$chk[0]){
				$data_new[0] = array('lang_code'=>$params[$i]['lang_code'],'name'=>$params[$i]['name'],'value'=>$params[$i]['value']);
				$sval = db_query("INSERT INTO ?:language_values ?m",$data_new);			
			}
			$i++;
		}
        echo "<script>alert('MOLPay Malaysia Online Payment was successfully created!');window.history.back();</script>"; exit;
    } else {
        echo "<script>alert('Already set up');window.history.back();</script>"; exit;
    }

}

if( $mode == "getChannel" ) {
    $sspp  = db_query("SELECT processor_id FROM ?:payment_processors WHERE processor = ?s", "MOLPay Malaysia Online Payment");
    $rwssp = mysqli_fetch_assoc($sspp);

    if( !empty($rwssp) && is_array($rwssp) ){
        $ssp   = db_query("SELECT payment_id, processor_params FROM ?:payments WHERE processor_id = ?i", $rwssp['processor_id']);
        $rwssp = mysqli_fetch_assoc($ssp);

        $data = unserialize($rwssp['processor_params']);

        if( is_array($data['channel']) && !empty($data['channel']) ) {
            $processor_params = json_encode($data['channel']); 
            echo $processor_params;
        } else {
            echo "-1"; 
        }
    }
	exit;
}
?>