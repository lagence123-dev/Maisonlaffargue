{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2020 PrestaShop SA
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}

    <div class="block_newsletter col-md-3 wrapper revealOnScroll animated fadeInUp links" data-animation="fadeInUp">
        <p class="h3 d-none d-md-block">{l s='Newsletter Signup' d='Shop.Theme.Global'}</p>
        <div class="title clearfix d-md-none" data-target="#footer_newsletter" data-toggle="collapse">
            <span class="h3 text-uppercase block-newsletter-title">{l s='Newsletter Signup' d='Shop.Theme.Global'}</span>
            <span class="float-xs-right">
                <span class="navbar-toggler collapse-icons">
                    <i class="mercury-icon-angle-bottom add"></i>
                    <i class="mercury-icon-angle-up remove"></i>
                </span>
            </span>
        </div>
        <div id="footer_newsletter" class="collapse">
            {*<p id="block-newsletter-label" class="col-md-5 col-12">{l s='Get our latest news and special sales' d='Shop.Theme.Global'}</p>*}
            {if $conditions}
            <p>{$conditions}</p>
            {/if}
            {if $msg}
            <p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
                {$msg}
            </p>
            {/if}
            {if isset($id_module)}
            {hook h='displayGDPRConsent' id_module=$id_module}
            {/if}
            <form action="{$urls.pages.index}#footer" method="post">
                <input class="btn btn-primary float-xs-right d-block d-sm-none" name="submitNewsletter" type="submit" value="{l s='OK' d='Shop.Theme.Actions'}">
                <div class="input-wrapper">
                    <input name="email" type="email" value="{$value}" placeholder="{l s='Your email address' d='Shop.Forms.Labels'}">
                    <button class="btn btn-primary btn-footer d-none d-sm-block" name="submitNewsletter" type="submit" value=""></button>
                </div>
                <input type="hidden" name="action" value="0">
                <div class="clearfix"></div>
            </form>
        </div>
    </div>