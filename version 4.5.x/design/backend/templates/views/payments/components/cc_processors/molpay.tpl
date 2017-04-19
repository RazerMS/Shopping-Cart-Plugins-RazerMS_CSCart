<p>Welcome to MOLPay Setup page</p>
<hr>

<div class="control-group">
    <label class="control-label" for="merchant_id">Merchant ID:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="verify_key">Verify Key:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][verify_key]" id="verify_key" value="{$processor_params.verify_key}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ab_currency">Currency</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="ab_currency">

            <option value="MYR"{if $processor_params.currency == "MYR"} selected="selected"{/if}>{__("currency_code_myr")}</option>
            <option value="THB"{if $processor_params.currency == "THB"} selected="selected"{/if}>{__("currency_code_thb")}</option>
            <!-- <option value="SGD"{if $processor_params.currency == "SGD"} selected="selected"{/if}>{__("currency_code_sgd")}</option> -->
            <option value="VND"{if $processor_params.currency == "VND"} selected="selected"{/if}>{("Vietnam Dong")}</option>
            <!-- <option value="IDR"{if $processor_params.currency == "IDR"} selected="selected"{/if}>{("Indonesia Rupiah")}</option> -->
            
        </select>
    </div>
</div>

<div class="control-group" id="panel-channel">
    <label class="control-label" for="channel">Channel</label>
    <div class="controls" id="channel"></div>
</div>

<div class="control-group">
    <label class="control-label" for="ab_mode">Test/Live Mode</label>
    <div class="controls">
        <select name="payment_data[processor_params][mode]" id="ab_mode">
            <option value="test"{if $processor_params.mode eq "test"} selected="selected"{/if}>{__("test")}</option>
            <option value="live"{if $processor_params.mode eq "live"} selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

{script src="js/tygh/molpay.js?v=3"}