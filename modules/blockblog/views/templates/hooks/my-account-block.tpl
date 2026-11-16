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

{if $blockblogava_on == 1}
    {if $blockblogislogged !=0}
        <li>

            <a href="{$blockbloguseraccount_url|escape:'htmlall':'UTF-8'}"
               title="{l s='Blog Author Avatar' mod='blockblog'}">
                <i class="fa fa-users"></i>
                {l s='Blog Author Avatar' mod='blockblog'}
            </a>
        </li>
    {/if}
{/if}


{if $blockblogmyblogposts_on == 1  && $blockblogis_show_customer == 1}
{if $blockblogislogged !=0}
    <li>

        <a href="{$blockblogmyblogposts_url|escape:'htmlall':'UTF-8'}"
           title="{l s='My Blog Posts' mod='blockblog'}">
            <i class="fa fa-newspaper-o"></i>
            {l s='My Blog Posts' mod='blockblog'}
        </a>
    </li>
{/if}
{/if}


{if $blockblogmyblogcom_on == 1  && $blockblogis_show_customer == 1}
{if $blockblogislogged !=0}
    <li>

        <a href="{$blockblogmyblogcomments_url|escape:'htmlall':'UTF-8'}"
           title="{l s='My Blog Comments' mod='blockblog'}">
            <i class="fa fa-comments-o"></i>
            {l s='My Blog Comments' mod='blockblog'}
        </a>
    </li>
{/if}
{/if}


{if $blockblogloyality_onl == 1}
    {if $blockblogislogged !=0}
        <li>

            <a href="{$blockblogloyalty_account_url|escape:'htmlall':'UTF-8'}"
               title="{l s='Blog Loyalty Program' mod='blockblog'}">
                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/loyalty-logo.png" alt="{l s='Loyalty Program' mod='blockblog'}" />
                {l s='Blog Loyalty Program' mod='blockblog'}
            </a>
        </li>
    {/if}
{/if}