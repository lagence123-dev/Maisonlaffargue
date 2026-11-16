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

        {if $blockblogis17 == 0}
            <li>
        {/if}


        <a href="{$blockbloguseraccount_url|escape:'htmlall':'UTF-8'}" {if $blockblogis17 == 1}class="col-lg-4 col-md-6 col-sm-6 col-xs-12"{/if}
           title="{l s='Blog Author Avatar' mod='blockblog'}">

            {if $blockblogis17 == 1}<span class="link-item">{/if}

                {if $blockblogis16 == 1}<i {if $blockblogis17 == 1}class="material-icons"{/if}>{/if}
                    <i class="fa fa-users fa-lg"></i>
                    {if $blockblogis16 == 1}</i>{/if}

                {if $blockblogis16 == 1  && $blockblogis17 == 0}<span>{/if}
                    {l s='Blog Author Avatar' mod='blockblog'}
                    {if $blockblogis16 == 1  && $blockblogis17 == 0}</span>{/if}

                {if $blockblogis17 == 1}</span>{/if}

        </a>

        {if $blockblogis17 == 0}
            </li>
        {/if}


    {/if}
{/if}



{if $blockblogmyblogposts_on == 1 && $blockblogis_show_customer == 1}
{if $blockblogislogged !=0}

    {if $blockblogis17 == 0}
        <li>
    {/if}


    <a href="{$blockblogmyblogposts_url|escape:'htmlall':'UTF-8'}" {if $blockblogis17 == 1}class="col-lg-4 col-md-6 col-sm-6 col-xs-12"{/if}
       title="{l s='My Blog Posts' mod='blockblog'}">

        {if $blockblogis17 == 1}<span class="link-item">{/if}

            {if $blockblogis16 == 1}<i {if $blockblogis17 == 1}class="material-icons"{/if}>{/if}
                <i class="fa fa-newspaper-o fa-lg"></i>
                {if $blockblogis16 == 1}</i>{/if}

            {if $blockblogis16 == 1  && $blockblogis17 == 0}<span>{/if}
                {l s='My Blog Posts' mod='blockblog'}
                {if $blockblogis16 == 1  && $blockblogis17 == 0}</span>{/if}

            {if $blockblogis17 == 1}</span>{/if}

    </a>

    {if $blockblogis17 == 0}
        </li>
    {/if}


{/if}
{/if}




{if $blockblogmyblogcom_on == 1 && $blockblogis_show_customer == 1}
{if $blockblogislogged !=0}

    {if $blockblogis17 == 0}
        <li>
    {/if}


    <a href="{$blockblogmyblogcomments_url|escape:'htmlall':'UTF-8'}" {if $blockblogis17 == 1}class="col-lg-4 col-md-6 col-sm-6 col-xs-12"{/if}
       title="{l s='My Blog Comments' mod='blockblog'}">

        {if $blockblogis17 == 1}<span class="link-item">{/if}

            {if $blockblogis16 == 1}<i {if $blockblogis17 == 1}class="material-icons"{/if}>{/if}
                <i class="fa fa-comments-o fa-lg"></i>
                {if $blockblogis16 == 1}</i>{/if}

            {if $blockblogis16 == 1  && $blockblogis17 == 0}<span>{/if}
                {l s='My Blog Comments' mod='blockblog'}
                {if $blockblogis16 == 1  && $blockblogis17 == 0}</span>{/if}

            {if $blockblogis17 == 1}</span>{/if}

    </a>

    {if $blockblogis17 == 0}
        </li>
    {/if}


{/if}
{/if}


{if $blockblogloyality_onl == 1}
    {if $blockblogislogged !=0}

        {if $blockblogis17 == 0}
            <li>
        {/if}


        <a href="{$blockblogloyalty_account_url|escape:'htmlall':'UTF-8'}" {if $blockblogis17 == 1}class="col-lg-4 col-md-6 col-sm-6 col-xs-12"{/if}
           title="{l s='Blog Loyalty Program' mod='blockblog'}">

            {if $blockblogis17 == 1}<span class="link-item">{/if}

                {if $blockblogis16 == 1}<i {if $blockblogis17 == 1}class="material-icons"{/if}>{/if}
                    <img class="icon" src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/loyalty-logo.png" />
                    {if $blockblogis16 == 1}</i>{/if}

                {if $blockblogis16 == 1}<span>{/if}
                    {l s='Blog Loyalty Program' mod='blockblog'}
                    {if $blockblogis16 == 1}</span>{/if}


                {if $blockblogis17 == 1}</span>{/if}

        </a>


        {if $blockblogis17 == 0}
            </li>
        {/if}

    {/if}
{/if}