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

{if $blockblogis16 == 1 && $blockblogis17 ==0}
        {capture name=path}<a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
            <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{l s='My Blog Posts' mod='blockblog'}{/capture}
    {/if}

    {if $blockblogis17 == 1}
        <a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
        <span class="navigation-pipe"> > </span>{l s='My Blog Posts' mod='blockblog'}
    {/if}

    {if $blockblogis16 == 1}
        <h3 class="page-product-heading">{l s='My Blog Posts' mod='blockblog'}</h3>

    {else}

        {l s='My Blog Posts' mod='blockblog'}

    {/if}


{if ($blockblogstatus_author == 0 || $blockblogstatus_author == 2) && $blockblogstatus_author !== ""}
<div class="advertise-text-blockblog advertise-text-blockblog-text-align">
    {l s='Customer disabled by admin' mod='blockblog'}
</div>
{else}

    {if $blockblogaction == "delete" && $blockblogid_item >0}
        <div id="confirm-reminder" class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>{l s='Post has been successfully deleted' mod='blockblog'}
        </div>
    {/if}


    <div class="add-post-myaccount">
        <a href="?action=add" class="btn-custom btn-success" title="{l s='Add Blog Post' mod='blockblog'}"><i class="fa fa-plus-square"></i>&nbsp;{l s='Add Blog Post' mod='blockblog'}</a>

        {* loyalty program *}
        {if $blockblogloyality_onl == 1}
            {if $blockblogloyality_add_blog_post_statusl == "loyality_add_blog_post"}

                {if $blockblogloyality_add_blog_post_timesul < $blockblogloyality_add_blog_post_timesl}
                    <br/><br/>
                    <div class="advertise-text-review">

                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/flag.png"
                             alt ="{$blockblogloyality_add_blog_post_pointsl|escape:'htmlall':'UTF-8'}"
                                />&nbsp;{$blockblogloyality_add_blog_post_descriptionl|replace:'[points]':$blockblogloyality_add_blog_post_pointsl nofilter}

                    </div>

                {/if}
            {/if}
        {/if}
        {* loyalty program *}
    </div>


    {if $blockblogmycount_all>0}
        <div class="block-center {if $blockblogis17 == 1}block-categories{/if}" id="block-history">
            <div id="gsniprev-list">
                <table id="block-history" class="table table-bordered {if $blockblogis16 == 1}responsive-table-custom{/if}">
                    <thead>
                    <tr>
                        <th class="first_item">
                            {l s='Image' mod='blockblog'}
                        </th>
                        <th>
                            {l s='Title' mod='blockblog'}
                        </th>
                        <th class="item">
                            {l s='Content' mod='blockblog'}
                        </th>
                        <th class="item">
                            {l s='Date Add' mod='blockblog'}
                        </th>
                        <th class="last_item">
                            {l s='Status' mod='blockblog'}
                        </th>
                        {if $blockblogis_editmyblogposts == 1}
                            <th class="item">
                                {l s='Edit' mod='blockblog'}
                            </th>
                        {/if}
                        {if $blockblogis_delmyblogposts == 1}
                            <th class="item">
                                {l s='Delete' mod='blockblog'}
                            </th>
                        {/if}

                    </tr>
                    </thead>
                    <tbody id="blog-items">


                    {assign var="list_myblogposts" value="modules/blockblog/views/templates/front/list_myblogposts.tpl"}

                    {if file_exists("{$tpl_dir}{$list_myblogposts}")}
                        {include file="{$tpl_dir}{$list_myblogposts}"}
                    {else}
                        {include file="{$list_myblogposts}"}
                    {/if}


                    </tbody>
                </table>

                <div id="page_nav">
                    {$blockblogpaging nofilter}
                </div>

            </div>


        </div>


    {else}
        <div class="advertise-text-blockblog advertise-text-blockblog-text-align">
            {l s='There are not My Blog Posts yet.' mod='blockblog'}
        </div>
    {/if}


{/if}

    <br/>
    <ul class="footer_links clearfix">
        <li class="float-left margin-right-10">
            <a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-blockblog">
                <span><i class="icon-chevron-left"></i> {l s='Home' mod='blockblog'}</span>
            </a>
        </li>
        <li class="float-left">
            <a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small-blockblog">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='blockblog'}
			</span>
            </a>
        </li>

    </ul>




{literal}
    <style type="text/css">


        @media
        only screen and (max-width: 760px),
        (min-device-width: 768px) and (max-device-width: 1024px)  {

        /* Force table to not be like tables anymore */
        table.responsive-table-custom, table.responsive-table-custom thead,
        table.responsive-table-custom tbody,
        table.responsive-table-custom th,
        table.responsive-table-custom td,
        table.responsive-table-custom tr {
            display: block!important;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        table.responsive-table-custom thead tr {
            position: absolute!important;;
            top: -9999px!important;;
            left: -9999px!important;;
        }

        table.responsive-table-custom tr { border: 1px solid #ccc; }

        table.responsive-table-custom td {
            /* Behave  like a "row" */
            border: none!important;;
            border-bottom: 1px solid #eee!important;;
            position: relative!important;;
            padding-left: 50%!important;;

        }

        table.responsive-table-custom td:before {
            /* Now like a table header */
            position: absolute!important;;
            /* Top/left values mimic padding */
            top: 6px!important;;
            left: 6px!important;;
            width: 45%!important;;
            padding-right: 10px!important;;
            white-space: nowrap!important;;
        }

        /*
        Label the data
        */
        table.responsive-table-custom td:nth-of-type(1):before { content: "{/literal}{l s='Image' mod='blockblog'}{literal}"; }
table.responsive-table-custom td:nth-of-type(2):before { content: "{/literal}{l s='Title' mod='blockblog'}{literal}"; }
table.responsive-table-custom td:nth-of-type(3):before { content: "{/literal}{l s='Content' mod='blockblog'}{literal}"; }
table.responsive-table-custom td:nth-of-type(4):before { content: "{/literal}{l s='Date Add' mod='blockblog'}{literal}"; }
table.responsive-table-custom td:nth-of-type(5):before { content: "{/literal}{l s='Status' mod='blockblog'}{literal}"; }
{/literal}{if $blockblogis_editmyblogposts == 1}{literal}
    table.responsive-table-custom td:nth-of-type(6):before { content: "{/literal}{l s='Edit' mod='blockblog'}{literal}"; }
{/literal}{/if}{literal}
        {/literal}{if $blockblogis_delmyblogposts == 1}{literal}
    table.responsive-table-custom td:nth-of-type({/literal}{if $blockblogis_editmyblogposts == 1}{literal}7{/literal}{else}{literal}6{/literal}{/if}{literal}):before { content: "{/literal}{l s='Delete' mod='blockblog'}{literal}"; }
{/literal}{/if}{literal}


        }



    </style>

{/literal}


