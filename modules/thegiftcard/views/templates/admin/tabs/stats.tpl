{*
* 2017 Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author Keyrnel
* @copyright  2017 - Keyrnel SARL
* @license commercial
* International Registered Trademark & Property of Keyrnel SARL
*}

{if isset($statistics['mail_error']) && $statistics['mail_error'] > 0}
<div class="alert alert-danger">
	<span>{$statistics['mail_error']|intval} {l s='emails have not been automatically sent to users. Use the following button to manually send them.' mod='thegiftcard'}</<span>
	<div class="btn-group"><a class="btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&sendEmails=1">Send emails</a></div>
</div>
{/if}

{if isset($statistics['currency_missing']) && $statistics['currency_missing'] > 0}
<div class="alert alert-warning">
	<span>{$statistics['currency_missing']|intval} {l s='currencies must be indexed' mod='thegiftcard'}</<span>
	<div class="btn-group"><a class="btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&indexCurrencies=1">Index currencies</a></div>
</div>
{/if}

<div class="panel">
	<div class="panel-heading">
		<i class="icon-bar-chart"></i> {l s='Statistics' mod='thegiftcard'}
	</div>
	<table class="table">
		<thead>
			<tr>
				<th class="center"><span class="title_box ">{l s='ID' mod='thegiftcard'}</span></th>
				<th><span class="title_box">{l s='Template' mod='thegiftcard'}</span></th>
				<th><span class="title_box">{l s='Amount' mod='thegiftcard'}</span></th>
				<th><span class="title_box">{l s='Order' mod='thegiftcard'}</span></th>
				<th><span class="title_box">{l s='Voucher code' mod='thegiftcard'}</span></th>
				<th><span class="title_box ">{l s='Date purchased' mod='thegiftcard'}</span></th>
				<th><span class="title_box ">{l s='Used' mod='thegiftcard'}</span></th>
				<th><span class="title_box ">{l s='Date used' mod='thegiftcard'}</span></th>
				<th><span class="title_box ">{l s='Active' mod='thegiftcard'}</span></th>
				<th><span class="title_box ">{l s='Expiration' mod='thegiftcard'}</span></th>
				<th><span class="title_box ">{l s='Email sent' mod='thegiftcard'}</span></th>
			</tr>
		</thead>
		<tbody>
			{if isset($statistics['giftcards']) && count($statistics['giftcards'])}
				{foreach $statistics['giftcards'] AS $giftcard}
				<tr {if $giftcard.should_be_sent}class="danger"{/if}>
					<td class="center">{$giftcard.id_giftcard|intval}</td>
					<td>
						<a href="{$giftcard.img_url|escape:'html':'UTF-8'}" class="fancybox">
						<img
							src="{$giftcard.img_url|escape:'html':'UTF-8'}"
							alt="{l s='gift card' mod='thegiftcard'}"
							title="{l s='gift card' mod='thegiftcard'}"
							style="max-width:80px"
							class="img-thumbnail" />
						</a>
					</td>
					<td>{Tools::displayPrice($giftcard.reduction_amount|intval, $giftcard.id_currency|intval)}</td>
					<td>{$giftcard.reference|escape:'html':'UTF-8'}</td>
					<td>{$giftcard.code|escape:'html':'UTF-8'}</td>
					<td>{dateFormat date=$giftcard.date_purschased full=1}</td>
					<td>
						<span class="badge {if $giftcard.badge_value==='none'}badge-danger{elseif $giftcard.badge_value==='partial'}badge-warning{else}badge-success{/if}">
							{Tools::displayPrice($giftcard.value|floatval, $giftcard.id_currency|intval)}
						</span>
					</td>
					<td>{dateFormat date=$giftcard.date_used full=1}</td>
					<td>{dateFormat date=$giftcard.date_from}</td>
					<td>{dateFormat date=$giftcard.date_to}</td>
					<td>
						{if $giftcard.sent}
							<span class="badge badge-success">{l s='Yes' mod='thegiftcard'}</span>
							<div class="btn-group"><a class="btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&resendEmail={$giftcard.id_giftcard|intval}">Resend email</a></div>
						{else}
							<span class="badge badge-danger">{l s='No' mod='thegiftcard'}</span>
						{/if}
					</td>
				</tr>
				{/foreach}
			{else}
				<td class="list-empty" colspan="11">
					<div class="list-empty-msg">
						<i class="icon-warning-sign list-empty-icon"></i>
						{l s='No records found' mod='thegiftcard'}
					</div>
				</td>
			{/if}
		</tbody>
	</table>
</div>
