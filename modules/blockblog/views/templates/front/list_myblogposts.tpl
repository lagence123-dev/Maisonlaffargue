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

{foreach from=$blockblogmyposts item=post}
    <tr class="{if $post.status == 0 || $post.status == 3 || $post.status == 4}no-active-my-blockblog{/if} pl-animater {$blockblogd_eff_shopmyposts|escape:'htmlall':'UTF-8'}">
        <td>
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
                             src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$post.img|escape:'htmlall':'UTF-8'}?rand={$blockblogrand|escape:'htmlall':'UTF-8'}">
                            </a>
                    </div>
            {else}
                &nbsp;
            {/if}
        </td>
        <td class="blockblog-my-title">
            <a title="{$post.title|escape:'htmlall':'UTF-8'}"
               href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$post.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$post.id|escape:'htmlall':'UTF-8'}
                          {/if}
                        " target="_blank"
                    >{$post.title|escape:'htmlall':'UTF-8'}</a>
        </td>

        <td>
                {if strlen($post.content)>0}
                        {$post.content|strip_tags|mb_substr:0:100 nofilter}{if strlen($post.content)>100}...{/if}
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
            {*{elseif $post.status == 4}
                <i class="fa fa-plus-square fa-2x" style="color:green" ></i>
                *}
            {else}

                <i class="fa fa-clock-o fa-2x" style="color:#a94442"></i>

            {/if}

        </td>
        {if $blockblogis_editmyblogposts == 1}
        <td class="blockblog-my-action-btn" {if $blockblogis16 == 0}style="padding: 0px;text-align:center"{/if}>
                <a class="{if $blockblogis16 == 1}btn btn-success{else}button{/if}" title="{l s='Edit' mod='blockblog'}"
                   href="{$blockblogmyblogposts_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}action=edit&id={$post.id|escape:'htmlall':'UTF-8'}">
                    <i class="fa fa-pencil-square-o"></i>

                    {l s='Edit' mod='blockblog'}
                </a>
        </td>
        {/if}
        {if $blockblogis_delmyblogposts == 1}
        <td class="blockblog-my-action-btn ">
                <a class="{if $blockblogis16 == 1}btn btn-danger{else}button{/if}" title="{l s='Delete' mod='blockblog'}"
                   {*href="{$blockblogmyblogposts_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}action=delete&id={$post.id|escape:'htmlall':'UTF-8'}"*}
                   href="javascript:void(0)"
                   onclick="return confirm_action_blocblog('{l s='Are you sure?' mod='blockblog'}','{$blockblogmyblogposts_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}action=delete&id={$post.id|escape:'htmlall':'UTF-8'}')"
                        >
                    <i class="fa fa-trash-o"></i>

                    {l s='Delete' mod='blockblog'}
                </a>

        </td>
        {/if}



    </tr>
{/foreach}


{if $blockblogd_eff_shopmyposts != "disable_all_effects"}
{literal}
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function(){
                blockblog_init_effects_myblogposts();
            });
        });
    </script>
{/literal}
{/if}