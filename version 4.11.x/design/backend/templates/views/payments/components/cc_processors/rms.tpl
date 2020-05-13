<p>Version : 4.11.5</p>
<p>Welcome to Razer Merchant Services Online Payment Setup Page</p>
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
    <label class="control-label" for="secret_key">Secret Key:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][secret_key]" id="secret_key" value="{$processor_params.secret_key}"  size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="ab_currency">Currency</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="ab_currency">
            <option value="">Please Select</option>
            <option value="MYR" {if $processor_params.currency == "MYR"} selected="selected"{/if}>{__("currency_code_myr")}</option>
            <option value="THB" {if $processor_params.currency == "THB"} selected="selected"{/if}>{__("currency_code_thb")}</option>
            <option value="VND" {if $processor_params.currency == "VND"} selected="selected"{/if}>{("Vietnam Dong")}</option>
            
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
            <option value="test"{if $processor_params.mode == "test"} selected="selected"{/if}>{__("Test")}</option>
            <option value="live"{if $processor_params.mode == "live"} selected="selected"{/if}>{__("Live")}</option>
        </select>
    </div>
</div>
{script src="js/tygh/rms.js?n=1"}