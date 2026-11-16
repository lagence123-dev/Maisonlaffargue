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
            <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{l s='Edit Blog Comment' mod='blockblog'}{/capture}
    {/if}

    {if $blockblogis17 == 1}
        <a href="{$blockblogmy_account|escape:'htmlall':'UTF-8'}">{l s='My account' mod='blockblog'}</a>
        <span class="navigation-pipe"> > </span>{l s='Edit Blog Comment' mod='blockblog'}
    {/if}

    {if $blockblogis16 == 1}
        <h3 class="page-product-heading">{l s='Edit Blog Comment' mod='blockblog'}</h3>

    {else}

        {l s='Edit Blog Comment' mod='blockblog'}

    {/if}


    {if $blockblogaction == "edit" && $blockblogid_item >0 && count($blockblogerrors)>0}
        <div id="confirm-reminder" class="alert alert-danger">
            <button data-dismiss="alert" class="close" type="button">×</button>
            {foreach from=$blockblogerrors item=erroritem}
                {$erroritem|escape:'htmlall':'UTF-8'}<br/>
            {/foreach}

        </div>
    {/if}


    <div class="block-center {if $blockblogis17 == 1}block-categories{/if}" id="leaveComment">


        <form action="" method="post" class="form-horizontal add-comment-form" id="commentform">

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
                <label class="control-label col-xs-3-custom">{l s='Post' mod='blockblog'}:</label>
                <div class="col-xs-9-custom font-size-13">

                    {if strlen($blockblogmycomments.img)>0}
                        <div class="blockblog-my-img float-left">
                            <a title="{$blockblogmycomments.title|escape:'htmlall':'UTF-8'}"
                               href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogmycomments.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogmycomments.id|escape:'htmlall':'UTF-8'}
                          {/if}
                        " target="_blank"
                                    >
                                <img class="img-responsive" alt="{$blockblogmycomments.title|escape:'htmlall':'UTF-8'}"
                                     src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blockblogmycomments.img|escape:'htmlall':'UTF-8'}">
                            </a>
                        </div>
                    {else}
                        &nbsp;
                    {/if}

                    <a title="{$blockblogmycomments.title|escape:'htmlall':'UTF-8'}"
                       href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogmycomments.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blockblogmycomments.id|escape:'htmlall':'UTF-8'}
                          {/if}
                        " target="_blank" class="edit-comment-myaccount"
                            >{$blockblogmycomments.title|escape:'htmlall':'UTF-8'}</a>

                    <div class="clear"></div>

                </div>
                <div class="clear"></div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-3-custom" for="comment-blockblog">{l s='Comment' mod='blockblog'}:<span class="req">*</span></label>
                <div class="col-xs-9-custom">
                    <textarea rows="3" class="form-control" id="comment-blockblog" name="comment-blockblog">{$blockblogcomment nofilter}</textarea>
                    <div id="error_comment-blockblog" class="errorTxtAdd"></div>
                </div>
                <div class="clear"></div>

            </div>
            <div class="form-group">
                <label class="control-label col-xs-3-custom" for="rating-blockblog">{l s='Rating' mod='blockblog'}:<span class="req">*</span></label>
                <div class="col-xs-9-custom">

                    <input type="number" name="rating-blockblog" id="rating-blockblog" class="rating" value="{$blockblograting|escape:'htmlall':'UTF-8'}"/>

                    <div id="error_rating-blockblog" class="errorTxtAdd"></div>
                </div>
                <div class="clear"></div>

            </div>

            <div class="form-group">
                <label class="control-label col-xs-3-custom">{l s='Date add' mod='blockblog'}:</label>
                <div class="col-xs-9-custom font-size-13">
                    {$blockblogmycomments.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}
                </div>
                <div class="clear"></div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-3-custom">{l s='Status' mod='blockblog'}:</label>
                <div class="col-xs-9-custom font-size-13">
                    {if $blockblogmycomments.status == 1}
                        <img alt="{l s='Enabled' mod='blockblog'}" title="{l s='Enabled' mod='blockblog'}"
                             src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}img/admin/enabled.gif"/>

                    {elseif $blockblogmycomments.status == 3}
                        <i class="fa fa-pencil-square-o fa-2x" style="color:orange" ></i>
                    {else}

                        <i class="fa fa-clock-o fa-2x" style="color:#a94442"></i>

                    {/if}


                </div>
                <div class="clear"></div>
            </div>

            <div class="form-group">
                <div class="col-xs-offset-3-custom col-xs-9-custom">


                    <input type="submit" class="btn-custom btn-danger" name="blockblogcancel" value="{l s='Cancel' mod='blockblog'}"/>
                    <input type="submit" class="btn-custom btn-success" name="blockblogupdate" value="{l s='Update' mod='blockblog'}"/>


                </div>
                <div class="clear"></div>

            </div>

            <div class="clear"></div>
        </form>


    </div>






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





