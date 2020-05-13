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

{script src="js/tygh/rms.js"}
<script>
var myVar = setInterval(myTimer, 10000);
function myTimer() {
	if(document.body.contains(document.getElementById('payment_method'))){
		if(document.getElementById('payment_method').innerHTML.length == 1){
			loadPaymentMethod();
		}
    }
}

function getChannelByCurrency(currency){
                var channel = {};
                switch(currency){
                        case 'MYR':
                                channel = {                                   
                                    'credit'            : 'Credit Card/ Debit Card',
                                    'FPX_ABB'           : 'FPX Affin Bank (Affin Online)',
                                    'FPX_ABMB'          : 'FPX Alliance Bank Malaysia Berhad',
                                    'AMOnline'          : 'FPX Am Bank (Am Online)',
                                    'BIMB'              : 'FPX Bank Islam',
                                    'FPX_BKRM'          : 'FPX Bank Kerjasama Rakyat Malaysia',
                                    'bankmuamalat'      : 'FPX Bank Muamalat',
                                    'FPX_BSN'           : 'FPX Bank Simpanan Nasional',
                                    'CIMBCLICKS'        : 'FPX CIMB Bank (CIMB Clicks)',
                                    'HLBConnect'        : 'FPX Hong Leong Bank (HLB Connect)',
                                    'FPX_HSBC'          : 'FPX HSBC',
                                    'FPX_KFH'           : 'FPX Kuwait Finance House',
                                    'MB2U'              : 'FPX Maybank (Maybank2u)',
                                    'FPX_OCBC'          : 'FPX OCBC Bank',
                                    'PBB'               : 'FPX PublicBank (PBB Online)',
                                    'RHBNow'            : 'FPX RHB Bank (RHB Now)',
                                    'FPX_SCB'           : 'FPX Standard Chartered Bank',
                                    'FPX_UOB'           : 'FPX United Oversea Bank',
                                    'cash-711'          : '7-Eleven (Razer Cash)',
                                    'jompay'            : 'jomPAY',
                                    'BOOST'             : 'BOOST',
                                    'MB2U_QRPay-Push'   : 'Maybank QRPay(Push)',
                                    'RazerPay'          : 'RazerPay',
                                    'TNG-EWALLET'       : 'Touch `n Go eWallet',
                                    'WeChatPayMY'       : 'WeChatPay'
                                }
                                break;
                        case 'THB':
                                channel = {
                                        'th_pb_scbpn'   : 'SCBPN',
                                        'th_pb_ktbpn'   : 'KTBPN',
                                        'th_pb_baypn'   : 'BAYPN',
                                        'th_pb_bblpn'   : 'BBLPN',
                                        'th_pb_cash'    : 'CASH',
                                }
                                break;
                        case 'VND':
                                channel = {
                                        'vtc-vietcombank'       : 'Vietcom Bank',
                                        'vtc-techcombank'       : 'Techcom Bank'
                                }
                        break;
                }

                return channel;
        }

function loadPaymentMethod() {
        $.get("./?dispatch=rms.getChannel", function(data){ // Preset value from DB
                $("div#channel").empty();

                var str = data.split("<!DOCTYPE html>")[0];

                if( str != "-1" ){
                        var arrayChannel = JSON.parse(str);

                        var currency = $("#ab_currency").val();
                        var resultArray = getChannelByCurrency(currency);
                        var output = "";

                        $.each(resultArray, function(key, value){
                                output += "<li style='list-style-type:none;'>";
                                output += "<input type='checkbox' style='margin-right:10px;float:left;' name='payment_data[processor_params][channel][]' value='"+key+"'";

                                for(var i=0; i<arrayChannel.length; i++){
                                        if( arrayChannel[i] == key ){
                                                output += " checked='checked'";

                                        } else {
                                                output += "";
                                        }
                                }
                                output += ">"+value;
                                output += " </li>";
                        });


                        $("div#channel").append(output);
                } else {
                        return false;
                }

                setTimeout(function(){
                        for(var j=0; j<arrayChannel.length; j++){
                                $("#payment_method").append("<div class='col-md-2 col-xs-6 marginbttm text-center "+currency+"'><label class='hand' for='payment"+arrayChannel[j]+"'><img style='width: 120px;height: 43px;border-radius: 6px;border: 1px solid #b9b9b9;box-shadow: 1px 0px 3px #b9b9b9;' src='images/rms/"+arrayChannel[j]+".jpg' /></label><br /><input style='margin: 8px 0px 0px 0px;' type='radio' name='payment_options' id='payment"+arrayChannel[j]+"' value='"+arrayChannel[j]+"' required/></div>");
                        }
                }, 3000);
        });

        $(document).on("change", "#ab_currency", function(){
                var currency = $(this).val();
                var resultArray;

                $("div#channel").empty();
                if( currency.length !== 0 ) {
                        resultArray = getChannelByCurrency(currency);
                        $.each(resultArray, function(index, value) {
                                $("div#channel").append("<li style='list-style-type:none;'><input style='margin-right:10px;float:left;' type='checkbox' name='payment_data[processor_params][channel][]' value='"+index+"' />"+value+"</li>");
                        });
                }
        });
}

</script>
<p class="smalltxt">

Please select a payment type from below to proceed for payment.

</p>



<div class="row" id="payment_method" style="background: white; border-radius: 5px; border: 1px solid #f2f2f2; margin-left: 0px;">
</div>