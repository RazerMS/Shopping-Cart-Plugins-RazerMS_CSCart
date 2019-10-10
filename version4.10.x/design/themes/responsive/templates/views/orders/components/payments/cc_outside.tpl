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

{script src="js/tygh/molpay.js"}

<script>
var myVar = setInterval(myTimer, 5000);

function myTimer() {
	if(document.body.contains(document.getElementById('payment_method'))){
		if(document.getElementById('payment_method').innerHTML.length == 1){
		location.reload();
		}
    }
}
</script>

<p class="smalltxt">

Please select a payment type from below to proceed for payment.

</p>



<div class="row" id="payment_method" style="background: white; border-radius: 5px; border: 1px solid #f2f2f2; margin-left: 0px;">
</div>