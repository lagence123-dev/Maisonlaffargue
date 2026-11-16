{*
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 *}
 
<input type="hidden" id="checkUpgradeUrl" value="{$checkUpgradeUrl|escape:'html':'UTF-8'}" />
<input type="hidden" class="needUpgrade_zentria" value="{$needUpgrade|escape:'html':'UTF-8'}" />

<div class="modalUpgrade_zentria modalUpgrade bootstrap {if ! isset($need_upgrade) || (isset($need_upgrade) && ! $need_upgrade)}hidden{/if}">
	<div class="modalTitle">{l s='Your module' mod='zentria'} {$displayName|escape:'html':'UTF-8'} {l s='is not up to date' mod='zentria'}</div>
	<div class="modalContent">{l s='Your scans will still work, but to benefit from all the features, you need to update it.' mod='zentria'}</div>
	<a href="{$upgradeUrl|escape:'html':'UTF-8'}" class="btn btn-default">{l s='Upgrade' mod='zentria'}</a>
	<a href="javascript:hideUpgradeModal_zentria();" class="btn btn-warning">{l s='Cancel' mod='zentria'}</a>
</div>

<div class="modalUpgrade_zentria_shadow modalUpgradeshadow {if ! isset($need_upgrade) || (isset($need_upgrade) && ! $need_upgrade)}hidden{/if}"></div>
