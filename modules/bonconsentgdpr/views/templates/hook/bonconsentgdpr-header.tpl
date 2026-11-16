{*
 * 2015-2020 Bonpresta
 *
 * Bonpresta HTML Content
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 *  @author    Bonpresta
 *  @copyright 2015-2020 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}

{if $bon_consent_gdpr_show_change_preferences == false}
  <style>
    .cc-nb-changep {
      display: none !important;
    }
  </style>
{/if}

 
<script type="text/javascript" src="{_MODULE_DIR_}bonconsentgdpr/views/js/cookie-consent.js" charset="UTF-8"></script>
{* <script type="text/javascript" src="//www.termsfeed.com/public/cookie-consent/4.1.0/cookie-consent.js" charset="UTF-8"></script> *}

{literal}
  <script type="text/javascript" charset="UTF-8">
    document.addEventListener('DOMContentLoaded', function() {
      cookieconsent.run({"notice_banner_type":"{/literal}{$bon_consent_gdpr_style}{literal}","consent_type":"express","palette":"dark","language":"en","page_load_consent_levels":["strictly-necessary"],"notice_banner_reject_button_hide":false,"preferences_center_close_button_hide":false,"page_refresh_confirmation_buttons":false,"website_privacy_policy_url":"{/literal}{$bon_consent_gdpr_color_privacy_link}{literal}"});
    });
  </script>
{/literal}


{if isset($items) && $items}
  {foreach from=$items item=item name=item}
    <script type="text/plain" data-cookie-consent="{$item.cookie_type}">
      {$item.description nofilter}
    </script>
  {/foreach}
{/if}