{*
*  @author    Amazzing <modules@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{$all_fields = $cb->getWrapperSettingsFields($id_wrapper)}
{$current_type = $all_fields.general.display_type.value}
{$extra_type_ids = ['carousel' => 2, 'grid' => 5]}
<div class="cb-wrapper w-{$current_type|intval}{if !$banners} empty{/if}" data-id="{$id_wrapper|intval}">
    <div class="w-actions">
        {foreach $all_fields as $type => $fields}
            <form class="w-settings-form{if isset($extra_type_ids[$type])} w-{$extra_type_ids[$type]|escape:'html':'UTF-8'}-el form-horizontal panel" style="display:none;{/if}">
                <input type="hidden" name="id_wrapper" value="{$id_wrapper|intval}">
                <input type="hidden" name="type" value="{$type|escape:'html':'UTF-8'}">
                {$group_open = false}
                {foreach $fields as $n => $f}
                    {if !empty($f.group_start)}
                        {if !$group_open}{$group_open = true}{else}</div>{/if}
                        <div class="{$f.group_start|escape:'html':'UTF-8'}">
                    {/if}
                    {include file="./form-group.tpl" name = "settings[$n]" field = $f}
                {/foreach}
                {if $group_open}</div>{/if}
                {if $type == 'general'}
                    <button type="button" class="callSettings extra btn btn-default{if !in_array($current_type, $extra_type_ids)} hidden{/if}" data-show="{implode(',', $extra_type_ids)}">
                        <i class="icon-wrench"></i> {l s='Configure' mod='custombanners'}
                    </button>
                    <a href="#" class="addBanner pull-right">
                        <i class="icon-plus"></i> <span class="btn-txt">{l s='New banner' mod='custombanners'}</span>
                    </a>
                {else}
                    <div class="p-footer">
                    <button class="btn btn-default saveExtraSettings">
                        <i class="process-icon-save"></i> {l s='Save' mod='custombanners'}
                    </button>
                </div>
                {/if}
            </form>
        {/foreach}
        <a href="#" class="deleteWrapper" title="{l s='Delete wrapper' mod='custombanners'}"><i class="icon-trash"></i></a>
        <a href="#" class="dragger w-dragger"><i class="icon icon-arrows-v"></i></a>
    </div>
    <div class="cb-list">
        {foreach $banners as $banner}
            {include file="./banner-form.tpl" banner = $banner}
        {/foreach}
    </div>
</div>
{* since 3.0.2 *}
