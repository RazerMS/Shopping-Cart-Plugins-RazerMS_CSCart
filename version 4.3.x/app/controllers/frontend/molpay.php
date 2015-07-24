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
        echo "<script>alert('MOLPay Malaysia Online Payment was successfully created!');location.href = '/admin/index.php';</script>";
    } else {
        echo "<script>alert('Already set up');location.href = '/admin/index.php';</script>";
    }

    exit;
}
?>