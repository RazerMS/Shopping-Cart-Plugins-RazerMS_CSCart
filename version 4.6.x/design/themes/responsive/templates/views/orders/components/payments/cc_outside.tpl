<style>

.col-xs-6

{

float: left; 

width: auto; 

}



.marginbttm{

padding: 7px;

}

</style>

{if $cart.payment_method_data.processor_params.mode == 'test'}
{script src="https://sandbox.molpay.com/MOLPay/API/seamless/latest/js/MOLPay_seamless.deco.js"}
{else}
{script src="https://www.onlinepayment.com.my/MOLPay/API/seamless/latest/js/MOLPay_seamless.deco.js"}
{/if}
{script src="js/tygh/molpay.js"}



<p class="smalltxt">

Please select a payment type from below to proceed for payment.

</p>



<div class="row" id="payment_method" style="background: white; border-radius: 5px; border: 1px solid #f2f2f2; margin-left: 0px;">
</div>