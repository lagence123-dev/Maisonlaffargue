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

{extends file='page.tpl'}








{block name="content_wrapper"}

{block name="left_column"}
    {if isset($blockblogsidebar_posblog_mbposts_alias) && $blockblogsidebar_posblog_mbposts_alias == 1}
        <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
            {hook h="displayLeftColumn"}
        </div>
    {/if}
{/block}


<div id="content-wrapper" class="card card-block {if isset($blockblogsidebar_posblog_mbposts_alias) && $blockblogsidebar_posblog_mbposts_alias == 1}left-column col-xs-12 col-sm-8 col-md-9{elseif isset($blockblogsidebar_posblog_mbposts_alias) && $blockblogsidebar_posblog_mbposts_alias == 2}right-column col-xs-12 col-sm-8 col-md-9{/if}">
    {block name="page_content"}


    {if $blockblogis16 == 1 && $blockblogis17 ==0}
    {capture name=path}<a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{if $blockblogaction == "add"}{l s='Add Blog Post' mod='blockblog'}{else}{l s='Edit Blog Post' mod='blockblog'}{/if}{/capture}
    {/if}

    {if $blockblogis17 == 1}
        <a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
        <span class="navigation-pipe"> > </span>{if $blockblogaction == "add"}{l s='Add Blog Post' mod='blockblog'}{else}{l s='Edit Blog Post' mod='blockblog'}{/if}
    {/if}



    {if $blockblogis16 == 1}
        <h3 class="page-product-heading">{if $blockblogaction == "add"}{l s='Add Blog Post' mod='blockblog'}{else}{l s='Edit Blog Post' mod='blockblog'}{/if}</h3>

    {else}

        {if $blockblogaction == "add"}{l s='Add Blog Post' mod='blockblog'}{else}{l s='Edit Blog Post' mod='blockblog'}{/if}

    {/if}


    {if (($blockblogaction == "edit" && $blockblogid_item >0) || ($blockblogaction == "add")) && count($blockblogerrors)>0}
        <div id="confirm-reminder" class="alert alert-danger">
            <button data-dismiss="alert" class="close" type="button">×</button>
            {foreach from=$blockblogerrors item=erroritem}
                {$erroritem|escape:'htmlall':'UTF-8'}<br/>
            {/foreach}

        </div>
    {/if}

     {* loyalty program *}
    {if $blockblogloyality_onl == 1}
    {if $blockblogloyality_add_blog_post_statusl == "loyality_add_blog_post"}

    {if $blockblogloyality_add_blog_post_timesul < $blockblogloyality_add_blog_post_timesl}

        <div class="advertise-text-review">

            <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/flag.png"
                 alt ="{$blockblogloyality_add_blog_post_pointsl|escape:'htmlall':'UTF-8'}"
                    />&nbsp;{$blockblogloyality_add_blog_post_descriptionl|replace:'[points]':$blockblogloyality_add_blog_post_pointsl nofilter}

        </div>

    {/if}
    {/if}
    {/if}
        {* loyalty program *}

    <div class="block-center {if $blockblogis17 == 1}block-categories{/if}" id="leaveComment">


        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal add-comment-form gdpr-blockblog" id="commentform">

            {if $blockblogava_on == 1}
                <div class="form-group">
                    <label class="control-label col-xs-3-custom">{l s='Avatar' mod='blockblog'}:</label>
                    <div class="col-xs-9-custom">
                        {if strlen($blockblogc_avatar)>0}
                            <div class="avatar-blockblog-post-form">
                                <img src="{$blockblogc_avatar|escape:'htmlall':'UTF-8'}" alt="{$blockblogname_c|escape:'htmlall':'UTF-8'}" />
                            </div>
                        {/if}
                    </div>
                    <div class="clear"></div>
                </div>
            {/if}

            <div class="form-group">
                <label class="control-label col-xs-3-custom" for="post-title-blockblog">{l s='Blog Post Title' mod='blockblog'}:<span class="req">*</span></label>
                <div class="col-xs-9-custom">

                    <input type="text" class="form-control" id="post-title-blockblog" name="post-title-blockblog" value="{$blockblogtitle nofilter}"  />
                    <div id="error_title-blockblog" class="errorTxtAdd"></div>
                </div>
                <div class="clear"></div>

            </div>


            <div class="form-group">
                <label class="control-label col-xs-3-custom" for="post-content-blockblog">{l s='Blog Post Content' mod='blockblog'}:<span class="req">*</span></label>
                <div class="col-xs-9-custom">
                    <textarea rows="3" class="form-control" id="post-content-blockblog" name="post-content-blockblog">{$blockblogcomment nofilter}</textarea>

                    <div id="error_content-blockblog" class="errorTxtAdd"></div>
                </div>
                <div class="clear"></div>

            </div>

            <div class="form-group">
                <label class="control-label col-xs-3-custom">{l s='Logo Image' mod='blockblog'}</label>
                <div class="col-xs-9-custom font-size-13">

                    <input type="file" name="post_image" id="post_image" />

                    <div class="b-guide">
                        {l s='Allow formats' mod='blockblog'} *.jpg; *.jpeg; *.png; *.gif.
                    </div>


                    {if isset($blockblogpost['img']) && strlen($blockblogpost['img'])>0}
                        <div id="post_images_list">

                        <img class="img-thumbnail" alt="{$blockblogtitle|escape:'htmlall':'UTF-8'}"
                             src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blockblogpost['img']|escape:'htmlall':'UTF-8'}?rand={$blockblogrand|escape:'htmlall':'UTF-8'}">
                        <input type="radio" name="post_images" checked="" class="display-none"/>

                        <br/>

                        <a class="delete_product_image btn-custom btn-default-custom margin-top-10" href="javascript:void(0)"
                           onclick = "delete_img({$blockblogpost['id']|escape:'htmlall':'UTF-8'});"
                           >
                            <i class="fa fa-trash"></i> {l s='Delete logo image' mod='blockblog'}
                        </a>
                        </div>
                    {else}


                        {if $blockblogis_demo == 0}

                            {* loyalty program *}
                            {if $blockblogloyality_onl == 1}

                                {if $blockblogloyality_add_blog_post_logo_statusl == "loyality_add_blog_post_logo"}

                                    {if $blockblogloyality_add_blog_post_logo_timesul < $blockblogloyality_add_blog_post_logo_timesl}

                                        <div class="advertise-text-review">

                                            <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/flag.png"
                                                 alt ="{$blockblogloyality_add_blog_post_logo_pointsl|escape:'htmlall':'UTF-8'}"
                                                    />&nbsp;{$blockblogloyality_add_blog_post_logo_descriptionl|replace:'[points]':$blockblogloyality_add_blog_post_logo_pointsl nofilter}

                                        </div>

                                    {/if}
                                {/if}
                            {/if}
                            {* loyalty program *}
                        {/if}

                    {/if}


                    {if $blockblogis_demo == 1}
                    <div class="bootstrap">
                        <div class="alert alert-warning">
                            <button type="button" data-dismiss="alert" class="close">×</button>
                            <strong>Warning</strong><br>
                            Feature disabled on the demo mode
                            &zwnj;</div>
                    </div>
                    {/if}



                </div>
                <div class="clear"></div>
            </div>

            {if count($blockblogrel_cat)>0}
            <div class="form-group">
                <label class="control-label col-xs-3-custom">{l s='Select categories' mod='blockblog'}:</label>
                <div class="col-xs-9-custom">

                    <div class="panel" style="height:200px; overflow-x:hidden; overflow-y:scroll;">


                        <table width="100%" cellspacing="0" cellpadding="0" class="table table-my-select-category">
                            <tbody>
                            {assign var=i value=0}
                            {foreach $blockblogrel_cat as $_item}
                                <tr>
                                    <td align="center">
                                        <input type="checkbox" class="input_shop" {if $_item['id']|in_array:$blockblogcategory_ids}checked="checked"{/if}
                                               value="{$_item['id']|escape:'htmlall':'UTF-8'}" name="ids_categories[]">
                                    </td>

                                    <td class="my-select-category">
                                        <div class="row-custom">
                                        {if strlen($_item['img'])>0}
                                            <div class="col-sm-2-custom text-align-center">
                                                    <img class="img-responsive" alt="{$_item['title']|escape:'htmlall':'UTF-8'}"
                                                         src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$_item['img']|escape:'htmlall':'UTF-8'}">
                                            </div>
                                            {else}
                                            <div class="col-sm-2-custom">&nbsp;</div>
                                        {/if}

                                        <div class="col-sm-10-custom">

                                        <a href="{if $blockblogurlrewrite_on == 1}
                                                {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$_item['seo_url']|escape:'htmlall':'UTF-8'}
                                             {else}
                                                {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$_item['id']|escape:'htmlall':'UTF-8'}
                                             {/if}"
                                           title="{$_item['title']|escape:'htmlall':'UTF-8'}" target="_blank">{$_item['title']|escape:'htmlall':'UTF-8'}</a>
                                        </div>

                                        </div>

                                    </td>

                                </tr>
                                {assign var=i value=$i++}
                            {/foreach}
                            </tbody>
                        </table>


                    </div>

                </div>
                <div class="clear"></div>
            </div>
            {/if}


            {if $blockblogaction == "edit"}
            <div class="form-group">
                <label class="control-label col-xs-3-custom">{l s='Date add' mod='blockblog'}:</label>
                <div class="col-xs-9-custom font-size-13">
                    {$blockblogpost.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}
                </div>
                <div class="clear"></div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-3-custom">{l s='Status' mod='blockblog'}:</label>
                <div class="col-xs-9-custom font-size-13">
                    {if $blockblogpost.status == 1}
                        <img alt="{l s='Enabled' mod='blockblog'}" title="{l s='Enabled' mod='blockblog'}"
                             src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}img/admin/enabled.gif"/>

                    {elseif $blockblogpost.status == 3}
                        <i class="fa fa-pencil-square-o fa-2x" style="color:orange" ></i>
                    {else}

                        <i class="fa fa-clock-o fa-2x" style="color:#a94442"></i>

                    {/if}


                </div>
                <div class="clear"></div>
            </div>
            {/if}


            {* gdpr *}
            {hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
            {* gdpr *}

            <div class="form-group">
                <div class="col-xs-offset-3-custom col-xs-9-custom">


                    <input type="submit" class="btn-custom btn-danger" name="blockblogcancel" value="{l s='Cancel' mod='blockblog'}"/>

                    <input type="submit" class="btn-custom btn-success" name="blockblogupdate" value="{if $blockblogaction == "edit"}{l s='Update' mod='blockblog'}{else}{l s='Add' mod='blockblog'}{/if}"/>


                </div>
                <div class="clear"></div>

            </div>

            <div class="clear"></div>
        </form>


    </div>


    {literal}

    <script type="text/javascript">
        var ajax_link_blockblog = '{/literal}{$blockblogajax_url|escape:'htmlall':'UTF-8'}{literal}';
        var blockblogid_module = {/literal}{$id_module|escape:'htmlall':'UTF-8'}{literal};

        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function(){

                //$R('#post-content-blockblog');
                $('#post-content-blockblog').redactor();

            });
        });




    </script>
    {/literal}




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




        </div>
    {/block}



    {block name="right_column"}
        {if isset($blockblogsidebar_posblog_mbposts_alias) && $blockblogsidebar_posblog_mbposts_alias == 2}
            <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
                {hook h="displayRightColumn"}
            </div>
        {/if}

    {/block}




{/block}
