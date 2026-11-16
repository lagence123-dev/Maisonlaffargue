<div id="{$key|strtolower|escape:'htmlall':'UTF-8'}-field" class="form-group{if isset($field.class) && $field.class} {$field.class|escape:'htmlall':'UTF-8'}{/if}">
    <label class="control-label col-lg-3">{$field.title|escape:'htmlall':'UTF-8'}</label>
    <div class="col-lg-9 colorPickerContainer">
        <div class="input-group col-lg-2"><input data-hex="true" type="color" name="{$key|escape:'htmlall':'UTF-8'}[0]" id="{$key|escape:'htmlall':'UTF-8'}_0" value="{if !isset($color1) && isset($field.default) && $field.default}{$field.default|escape:'htmlall':'UTF-8'}{else}{if isset($color1)}{$color1|escape:'htmlall':'UTF-8'}{/if}{/if}" size="20" class="color mColorPicker" />{if isset($field.suffix)}{$field.suffix|atm_nofilter}{/if}</div>
        &nbsp; <div class="input-group col-lg-2" {if !isset($color2)} style="display:none"{/if}><input data-hex="true" type="color" name="{$key|escape:'htmlall':'UTF-8'}[1]" id="{$key|escape:'htmlall':'UTF-8'}_1" value="{if isset($color2)}{$color2|escape:'htmlall':'UTF-8'}{/if}" size="20" class="color mColorPicker" />{if isset($field.suffix)}{$field.suffix|atm_nofilter}{/if}</div>
        &nbsp; <input type="checkbox" name="{$key|escape:'htmlall':'UTF-8'}_gradient" id="{$key|escape:'htmlall':'UTF-8'}_gradient" value="1" {if isset($color2)} checked="checked"{/if} /> &nbsp; <label for="{$key|escape:'htmlall':'UTF-8'}_gradient">{l s='Make a gradient' mod='pm_advancedtopmenu'}</label>

        {if isset($field.desc) && !empty($field.desc)}
            <p class="help-block">
                {$field.desc|escape:'htmlall':'UTF-8'}
            </p>
        {/if}
    </div>
</div>
<script type="text/javascript">
    $("input[name={$key|escape:'htmlall':'UTF-8'}_gradient]").on('click', function() {
	if (!$(this).is(':checked')) {
		$("#{$key|escape:'htmlall':'UTF-8'}_gradient input").val('');
	}
    showSpanIfChecked($(this), "#{$key|escape:'htmlall':'UTF-8'}_gradient");
});
</script>
