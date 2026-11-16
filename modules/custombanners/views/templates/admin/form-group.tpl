{*
*  @author    Amazzing <modules@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if empty($field.group_class)}{$field.group_class = 'form-group'}{/if}
{if empty($field.label_class)}{$field.label_class = 'control-label col-lg-3'}{/if}
{if empty($field.input_wrapper_class)}{$field.input_wrapper_class = 'col-lg-3'}{/if}
{if empty($field.input_class)}{$field.input_class = ''}{/if}
{if !isset($field.value)}{$field.value = ''}{/if}

<div class="{$field.group_class|escape:'html':'UTF-8'}{if !empty($field.class)} {$field.class|escape:'html':'UTF-8'}{/if}">
    <label class="{$field.label_class|escape:'html':'UTF-8'}">
        <span{if !empty($field.tooltip)} class="label-tooltip" data-toggle="tooltip" title="{$field.tooltip|escape:'html':'UTF-8'}"{/if}>
            {$field.label} {* can not be escaped, because of possible html entities *}
        </span>
    </label>
    <div class="{$field.input_wrapper_class|escape:'html':'UTF-8'}{if !empty($field.locked_overlay)} lockable-field locked{/if}">
        {if $field.type == 'switcher'}
            <select class="switch-select{if $field.value} yes{/if} {$field.input_class|escape:'html':'UTF-8'}" name="{$name|escape:'html':'UTF-8'}">
                <option value="0"{if empty($field.value)} selected{/if}>{l s='No' mod='custombanners'}</option>
                <option value="1"{if !empty($field.value)} selected{/if}>{l s='Yes' mod='custombanners'}</option>
            </select>
        {else if $field.type == 'select'}
            <select class="{$field.input_class|escape:'html':'UTF-8'}" name="{$name|escape:'html':'UTF-8'}" data-initial-value="{$field.value|escape:'html':'UTF-8'}">
                {foreach $field.options as $i => $opt}
                    <option value="{$i|escape:'html':'UTF-8'}"{if $field.value == $i} selected{/if}>{$opt|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
        {else}
            {$use_group = !empty($field.input_prefix) || !empty($field.input_suffix)}
            {if $use_group}
                <div class="input-group">
                {if !empty($field.input_prefix)}<span class="input-group-addon">{$field.input_prefix|escape:'html':'UTF-8'}</span>{/if}
            {/if}
            <input type="text" name="{$name|escape:'html':'UTF-8'}" value="{$field.value|escape:'html':'UTF-8'}" class="{$field.input_class|escape:'html':'UTF-8'}">
            {if $use_group}
                {if !empty($field.input_suffix)}<span class="input-group-addon">{$field.input_suffix|escape:'html':'UTF-8'}</span>{/if}
                </div>
            {/if}
            {if !empty($field.locked_overlay)}
                <div class="locked-overlay">{$field.locked_overlay|escape:'html':'UTF-8'}</div> <a href="#" class="icon icon-lock toggleLockedField"></a>
            {/if}
        {/if}
    </div>
</div>
{* since 3.0.2 *}
