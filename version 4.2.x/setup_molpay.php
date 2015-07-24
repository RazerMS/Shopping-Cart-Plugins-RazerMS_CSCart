<?php

    define('AREA', 'C');

    //require dirname(__FILE__) . '/prepare.php';
    require dirname(__FILE__) . '/init.php';

    $data['processor_id'] 	= "1001";
    $data['processor'] 		= "MOLPay Malaysia Online Payment";
    $data['processor_script'] 	= "molpay.php";
    $data['processor_template'] = "views/orders/components/payments/cc_outside.tpl";
    $data['admin_template'] 	= "molpay.tpl";
    $data['callback'] 		= "N";
    $data['type'] 		= "P";


    // SELECT MOLPay info to check
    $chk_molpay = db_get_row("select processor from ?:payment_processors where processor = 'MOLPay Malaysia Online Payment'");
    if ( $chk_molpay['processor']=="MOLPay Malaysia Online Payment" ) {
        echo "Plugin for MOLPay Online Payment Gateway already exist. <br><br>Please configure this plugin on Admin site."; //already insert
        exit;
    }


    // INSERT MOLPay Info into DB
    else {
	db_query("REPLACE INTO ?:payment_processors ?e", $data);

	// SELECT MOLPay info to check if successfully install.
	$chk_molpay = db_get_row("select processor from ?:payment_processors where processor = 'MOLPay Malaysia Online Payment'");
	
        if ( $chk_molpay['processor']=="MOLPay Malaysia Online Payment" ){
                echo "Plugin for MOLPay Online Payment Gateway successfully installed in your shopping cart. <br><br>Please configure this plugin on Admin site."; //Successfully insert
                exit;
        }

        else{
                echo "Error Occured. Try Again."; // Maybe Error occur when install
                exit;
        }
	
    }

?>