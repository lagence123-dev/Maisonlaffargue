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

{foreach from=$blockblogmycomments item=post}
    <tr class="{if $post.status == 0 || $post.status == 3}no-active-my-blockblog{/if} pl-animater {$blockblogd_eff_shopmycom|escape:'htmlall':'UTF-8'}">
        <td class="blockblog-my-title">
            {if strlen($post.img)>0}
                <div class="blockblog-my-img">
                    <a title="{$post.title|escape:'htmlall':'UTF-8'}"
                       href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$post.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$post.id|escape:'htmlall':'UTF-8'}
                          {/if}
                        " target="_blank"
                            >
                        <img class="img-responsive" alt="{$post.title|escape:'htmlall':'UTF-8'}"
                             src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$post.img|escape:'htmlall':'UTF-8'}">
                    </a>
                </div>
            {else}
                &nbsp;
            {/if}

            <a title="{$post.title|escape:'htmlall':'UTF-8'}"
               href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$post.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$post.id|escape:'htmlall':'UTF-8'}
                          {/if}
                        " target="_blank"
                    >{$post.title|escape:'htmlall':'UTF-8'}</a>
        </td>
        <td class="my-account-comments-rating">
            <span class="rating-input">
                    {if $post.rating != 0}
                        {for $foo=0 to 4}
                            {if $foo < $post.rating}
                                <i class="fa fa-star" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>

                            {else}
                                <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                            {/if}

                        {/for}

                    {else}

                        {for $foo=0 to 4}
                        <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                    {/for}
                    {/if}
                </span>
        </td>

        <td>
            {if strlen($post.comment)>0}
                {$post.comment nofilter}
            {else}
                &nbsp;
            {/if}
        </td>


        <td>
            {$post.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}
            {*{dateFormat date=$post.time_add|escape:'htmlall':'UTF-8' full=0}*}
        </td>
        <td>
            {if $post.status == 1}
                <img alt="{l s='Enabled' mod='blockblog'}" title="{l s='Enabled' mod='blockblog'}"
                     src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}img/admin/enabled.gif"/>
            {elseif $post.status == 3}
                <i class="fa fa-pencil-square-o fa-2x" style="color:orange" ></i>
            {else}

                <i class="fa fa-clock-o fa-2x" style="color:#a94442"></i>

            {/if}
        </td>
        {if $blockblogis_editmyblogcom == 1}
            <td class="blockblog-my-action-btn" {if $blockblogis16 == 0}style="padding: 0px;text-align:center"{/if}>
                <a class="{if $blockblogis16 == 1}btn btn-success{else}button{/if}" title="{l s='Edit' mod='blockblog'}"
                   href="{$blockblogmyblogcomments_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}action=edit&id={$post.id|escape:'htmlall':'UTF-8'}">
                    <i class="fa fa-pencil-square-o"></i>

                    {l s='Edit' mod='blockblog'}
                </a>
            </td>
        {/if}
        {if $blockblogis_delmyblogcom == 1}
            <td class="blockblog-my-action-btn ">
                <a class="{if $blockblogis16 == 1}btn btn-danger{else}button{/if}" title="{l s='Delete' mod='blockblog'}"
                   {*href="{$blockblogmyblogcomments_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}action=delete&id={$post.id|escape:'htmlall':'UTF-8'}"*}
                        href="javascript:void(0)"
                   onclick="return confirm_action_blocblog('{l s='Are you sure?' mod='blockblog'}','{$blockblogmyblogcomments_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}action=delete&id={$post.id|escape:'htmlall':'UTF-8'}')"
                        >
                    <i class="fa fa-trash-o"></i>

                    {l s='Delete' mod='blockblog'}
                </a>

            </td>
        {/if}



    </tr>
{/foreach}


{if $blockblogd_eff_shopmycom != "disable_all_effects"}
{literal}
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function(){
                blockblog_init_effects_myblogcomments();
            });
        });
    </script>
{/literal}
{/if}