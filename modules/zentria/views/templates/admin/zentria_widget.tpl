{*
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 *}
<div class="zentria-widget">
	{*	START MAIN CONTENT BAR WIDGET SCORE  *}
	<div id="zentria-widget-bar" class="zentria-widget-bar">
		{* Widget description on the left *}
		<div class="zentria-widget-left-container">
			<div class="zentria-img">
				<span class="zentria-short-logo-light"></span>
			</div>
			<div class="zentria-widget-text">
				<div class="zentria-description-text">
					{l s='Score' mod='zentria'}<br />Zentria
				</div>
			</div>
		</div>
		<div id="zentria-main-score" class="zentria-main-score show" {if $isScoring}onclick="toggleZentriaWidgetPopup('zentria-widget-popup')"{/if}>
			{if ! $globalScore}
				<div class="zentria-score cursor-default">
					<div class="zentria-widget-progress" data-value="-1">
						<svg>
							<circle class="zentria-progress-track zentria-track-light" cx="37.5" cy="37.5" r="32.5"></circle>
							<circle class="zentria-progress-indicator" cx="37.5" cy="37.5" r="32.5"></circle>
						</svg>
						<div class="zentria-progress-center-text zentria-text-light">
							<div class="zentria-progress-current-value">?</div>
						</div>
					</div>
				</div>
			{else}
				<div class="zentria-score cursor-pointer">
					<div class="zentria-widget-progress" data-value="{$globalScore|escape:'html':'UTF-8'}" style="stroke: {$globalColor|default:'#9ea4a2'}">
						<svg>
							<circle class="zentria-progress-track zentria-track-light" cx="37.5" cy="37.5" r="32.5"></circle>
							<circle class="zentria-progress-indicator" cx="37.5" cy="37.5" r="32.5"></circle>
						</svg>
						<div class="zentria-progress-center-text zentria-text-light">
							<div class="zentria-progress-current-value">0</div>
							<div class="zentria-progress-separator zentria-bg-light"></div>
							<div class="zentria-progress-max-value">100</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
		{* Open button *}
		<div class="zentria-widget-right-container">
			<div class="zentria-buttons">
				<div class="zentria-btn zentria-open-btn">
					<a href="{$ctaUrl|escape:'html':'UTF-8'}" target="_blank">
						{l s='Manage my shop' mod='zentria'}
					</a>
					{if $isScoring}
						<div class="toggle-btn-popup" onclick="toggleZentriaWidgetPopup('zentria-widget-popup')">
							<span class="separator"></span>
							<svg fill="#fff" width="15px" height="15px" viewBox="0 0 256 256" id="Flat" xmlns="http://www.w3.org/2000/svg">
								<path stroke-width="12" d="M208,164a3.98881,3.98881,0,0,1-2.82861-1.17139L128,85.65674,50.82861,162.82861a4.00026,4.00026,0,0,1-5.65722-5.65722l80-80a4.00147,4.00147,0,0,1,5.65722,0l80,80A4,4,0,0,1,208,164Z"/>
							</svg>
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
	{*	END MAIN CONTENT BAR WIDGET SCORE  *}

	{*	START CONTENT OF POPUP WIDGET SCORE  *}
	<div id="zentria-widget-popup" class="zentria-widget-popup">
		<div class="zentria-widget-popup-close-btn" onclick="toggleZentriaWidgetPopup('zentria-widget-popup')">
			<svg width="13px" height="13px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="#5b6b79"/>
			</svg>
		</div>
		<div class="zentria-widget-popup-top">
			<span class="zentria-logo-dark"></span>
		</div>

		<div class="zentria-widget-popup-middle">
			{if ! $isScoring}
				<div class="col-xs-12 zentria-no-scan">
					{l s='No scan done yet' mod='zentria'}
				</div>
			{else}
				{if $securityScore}
					<div class="zentria-widget-popup-col">
						<div class="zentria-widget-progress" data-value="{$securityScore|escape:'html':'UTF-8'}" style="stroke: {$securityColor|default:'#9ea4a2'}">
							<svg>
								<circle class="zentria-progress-track zentria-track-lightgray" cx="37.5" cy="37.5" r="32.5"></circle>
								<circle class="zentria-progress-indicator" cx="37.5" cy="37.5" r="32.5"></circle>
							</svg>
							<div class="zentria-progress-center-text zentria-text-dark">
								<div class="zentria-progress-current-value">0</div>
								<div class="zentria-progress-separator zentria-bg-dark"></div>
								<div class="zentria-progress-max-value">100</div>
							</div>
						</div>
						<span class="zentria-widget-progress-subtext">{l s='Security' mod='zentria'}</span>
					</div>
				{/if}
				{if $speedScore}
					<div class="zentria-widget-popup-col">
						<div class="zentria-widget-progress" data-value="{$speedScore|escape:'html':'UTF-8'}" style="stroke: {$speedColor|default:'#9ea4a2'}">
							<svg>
								<circle class="zentria-progress-track zentria-track-lightgray" cx="37.5" cy="37.5" r="32.5"></circle>
								<circle class="zentria-progress-indicator" cx="37.5" cy="37.5" r="32.5"></circle>
							</svg>
							<div class="zentria-progress-center-text zentria-text-dark">
								<div class="zentria-progress-current-value">0</div>
								<div class="zentria-progress-separator zentria-bg-dark"></div>
								<div class="zentria-progress-max-value">100</div>
							</div>
						</div>
						<span class="zentria-widget-progress-subtext">{l s='Speed' mod='zentria'}</span>
					</div>
				{/if}
			{/if}
		</div>

		<div class="zentria-widget-popup-end">
			<div class="zentria-buttons">
				<a href="{$ctaUrl|escape:'html':'UTF-8'}" target="_blank" class="zentria-btn zentria-cta-btn">
					{l s='Manage my shop' mod='zentria'}
				</a>
			</div>
		</div>
	</div>
	{*	END CONTENT OF POPUP WIDGET SCORE  *}
</div>