(function(_, $) {
        function getChannelByCurrency(currency){
                var channel = {};
                switch(currency){
                        case 'MYR':
                                channel = {
                                        'credit'                : 'Visa / MasterCard',
                                        'fpx'                   : 'FPX',
                                        'maybank2u'             : 'Maybank2u',
                                        'cimbclicks'            : 'CIMB Clicks',
                                        'hlb'                   : 'Hong Leong Bank',
                                        'rhb'                   : 'RHB Bank',
                                        'affinonline'           : 'Affin Bank',
                                        'amb'                   : 'AmBank',
                                        'pbe'                   : 'Public Bank',
                                        'bankislam'             : 'Bank Islam',
                                        'FPX_OCBC'              : 'OCBC Bank',
                                        'FPX_SCB'               : 'Standard Chartered Bank',
                                        'cash-711'              : '7 Eleven',
                                        'jompay'                : 'jomPAY'
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
                $("#place_order_tab2").removeAttr("type");
                var formx = $("#place_order_tab2").closest('form').attr('id','checkoutForm');
                $("#checkoutForm").attr('role','molpayseamless');
                var act = $("#checkoutForm").attr('action');

                $.get("./?dispatch=molpay.getChannel", function(data){ // Preset value from DB
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
                                        $("#payment_method").append("<div class='col-md-2 col-xs-6 marginbttm text-center "+currency+"'><label class='hand' for='payment"+arrayChannel[j]+"'><img src='images/"+arrayChannel[j]+".jpg' /></label><br /><input style='margin: 8px 0px 0px 50px;' type='radio' name='payment_options' id='payment"+arrayChannel[j]+"' value='"+arrayChannel[j]+"' required/></div>");
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

        $(document).ready(function(){
                if ($('input:radio[name="payment_id"]').length) {
                        var label = $("label[for='"+$('input[type=radio][name=payment_id]:checked').attr('id')+"']");
                        if(label.text().toUpperCase().indexOf("MOLPAY") !== -1) {
                                loadPaymentMethod();
                        }

                        $(document).on('change', 'input:radio[name="payment_id"]', function(){
                                var label = $("label[for='"+$('input[type=radio][name=payment_id]:checked').attr('id')+"']");
                                if(label.text().toUpperCase().indexOf("MOLPAY") !== -1) {
                                        loadPaymentMethod();
                                }
                        });
                } else {
                        loadPaymentMethod();
                }
        });

        $(document).on('click','.btn, .btn-primary', function(e){
                e.preventDefault();
                var formx = $(this).closest('form').attr('name');
                var act = $("form[name='"+formx+"']").attr("action");
                $("form[name='"+formx+"']").removeAttr("action");
                var url = act+"/?dispatch=payments.update";
                $("form[name='"+formx+"']").attr("action", url);
                var checkedAtLeastOne = $("input[name='payment_data[processor_params][channel][]']:checked").length > 0;
                if( checkedAtLeastOne ){
                        $("form[name='"+formx+"']").submit();
                }else{
                        alert("Please tick at least one channel before save.");
                        return false;
                }
        });

        $(document).on("click", "#place_order_tab2", function(e){
                e.preventDefault();
                $("#checkoutForm").trigger('submit');
        });
}(Tygh, Tygh.$));
