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

