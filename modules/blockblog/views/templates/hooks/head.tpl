{*
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockblog
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{if $blockblogis_blog != 0}

    <meta name="twitter:card" content="summary"/>

    <meta property="fb:app_id" content="{$blockblogappid|escape:'htmlall':'UTF-8'}"/>
    <meta property="fb:admins" content="{$blockblogappadmin|escape:'htmlall':'UTF-8'}">

    <meta property="og:title" content="{$blockblogname|escape:'htmlall':'UTF-8'}"/>
    {if strlen($blockblogimg) >0}
{*        <meta property="og:image" content="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $blockblogis_cloud == 1}modules/blockblog/upload/{else}upload/blockblog/{/if}{$blockblogimg|escape:'htmlall':'UTF-8'}"/>*}
        <meta property="og:image" content="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/upload/{$blockblogimg|escape:'htmlall':'UTF-8'}"/>
    {/if}
    <meta property="og:description" content="{$blockblogseod|escape:'htmlall':'UTF-8'}"/>
    <meta property="og:url" content="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogpostseo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogpostid|escape:'htmlall':'UTF-8'}{/if}"/>
    <meta property="og:type" content="product"/>


    <link rel="canonical" href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogpostseo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogpostid|escape:'htmlall':'UTF-8'}{/if}" />

{/if}




<!-- Module Blog PRO for PrestaShop -->
{literal}
<script type="text/javascript">
    var ajax_url_blockblog = '{/literal}{$blockblogajax_url nofilter}{literal}';

    {/literal}{if $blockbloggallery_on == 1}{literal}
    var slider_effect_blockblog = '{/literal}{$blockblogslider_effect nofilter}{literal}';
    var gallery_autoplay_blockblog = {/literal}{$blockbloggallery_autoplay nofilter}{literal};
    var gallery_speed_blockblog = {/literal}{$blockbloggallery_speed nofilter}{literal};
    {/literal}{/if}{literal}

</script>
{/literal}

{if $blockblogrsson == 1}
<link rel="alternate" type="application/rss+xml" href="{$blockblogrss_url|escape:'htmlall':'UTF-8'}" />
{/if}



{if $blockblogslider_b_on == 1 || $blockblogslider_h_on == 1}
{if count($blockblogslides)>0}
    <!-- slider blockblog -->
    {$blockblog_scripts nofilter}
    <!-- slider blockblog -->
    {/if}
{/if}


{literal}
<style type="text/css">

    .button.button-small-blockblog{
        background: {/literal}{$blockblogbgcolor_main|escape:'htmlall':'UTF-8'}{literal} none repeat scroll 0 0;
        border-color: {/literal}{$blockblogbgcolor_main|escape:'htmlall':'UTF-8'}{literal};
        border-radius:5px;
    }

    .button.button-small-blockblog:hover{
        background: {/literal}{$blockblogbgcolor_hover|escape:'htmlall':'UTF-8'}{literal} none repeat scroll 0 0;
        border-color: {/literal}{$blockblogbgcolor_hover|escape:'htmlall':'UTF-8'}{literal};
    }

    .button.button-small-blockblog span, .button.button-small-blockblog b{
         border: 1px solid {/literal}{$blockblogbgcolor_main|escape:'htmlall':'UTF-8'}{literal};
     }

    .button.button-small-blockblog span:hover, .button.button-small-blockblog b:hover{
        border: 1px solid {/literal}{$blockblogbgcolor_hover|escape:'htmlall':'UTF-8'}{literal};
    }

    .button-mini-blockblog, .button_mini_custom{
        background-color: {/literal}{$blockblogbgcolor_main|escape:'htmlall':'UTF-8'}{literal};
        border-radius:5px;
    }

    .button-mini-blockblog:hover, .button_mini_custom:hover{background-color: {/literal}{$blockblogbgcolor_hover|escape:'htmlall':'UTF-8'}{literal};}

</style>
{/literal}

{if $blockblogis_blog != 0}
{if $blockblogis_captcha_com == 1}
    {if $blockblogbcaptcha_type != 1}

{literal}
    <script async defer type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl={/literal}{$blockbloglang_captcha|escape:'htmlall':'UTF-8'}{literal}&{/literal}{if $blockblogbcaptcha_type == 3}{literal}render={/literal}{$blockblogbcaptcha_site_key|escape:'htmlall':'UTF-8'}{literal}{/literal}{/if}{literal}"></script>

    {/literal}{if $blockblogbcaptcha_type == 3}{literal}
        <script type="text/javascript">

          grecaptcha.ready(function() {
              grecaptcha.execute('{/literal}{$blockblogbcaptcha_site_key|escape:'htmlall':'UTF-8'}{literal}', {action: 'homepage'}).then(function(token) {

              });
          });

        </script>
    {/literal}{/if}{literal}


{/literal}

    {/if}
{/if}
{/if}

<!-- Module Blog PRO for PrestaShop -->


