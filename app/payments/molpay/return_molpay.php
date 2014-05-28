<?php
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 2014-05-28 - MOLPay Sdn Bhd
//
// Since version 4.1.5, parameter $_REQUEST['skey'] has been used by CS-Cart
// and conflicted with Molpay return parameter with the same name.
// This page was created to handle return parameter from Molpay and change the
// 'skey' parameter to 'molpay_key' and then redirect it to original CS-Cart 
// return page.
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if(!isset($_GET['key']) && !isset($_GET['order_id']))
  exit;

$current_url = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}".(substr($_SERVER['REQUEST_URI'],0,1)=='/' ? $_SERVER['REQUEST_URI'] : '/'.$_SERVER['REQUEST_URI']);

$pos = strpos($current_url,'app/payments/molpay/return_molpay.php');

if($pos)
  $redirect_url = substr($current_url, 0, $pos)."index.php?dispatch=payment_notification.return&payment=molpay&order_id={$_GET['order_id']}&key={$_GET['key']}";
else
  exit;

foreach($_POST as $k => $v)
{
  if($k=='skey')
  {
    $_POST['molpay_skey'] = $v;
    unset($_POST[$k]);
  }
}

echo "<html>
    <body>
    <form action='{$redirect_url}' method='post' name='molpay_form'>";
foreach($_POST as $k => $v)
{
  echo "<input type='hidden' name='{$k}' value='{$v}'>";
}
echo '<input type="submit" value="submit">';
echo '</form></body></html>';
?>