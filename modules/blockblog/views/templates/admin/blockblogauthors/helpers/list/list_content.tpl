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

{extends file="helpers/list/list_content.tpl"}
    {block name="td_content"}
        {if isset($params.type_custom) && $params.type_custom == 'is_active'}

            {if $tr[$key].value == 1 || $tr[$key].value == 0}

            <span class="comments-suggest-to-delete label-tooltip" data-original-title="{if $tr[$key].value == 0}{l s='The customer has disabled and blog posts is visible' mod='blockblog'}{else}{l s='The customer has enabled ' mod='blockblog'}{/if}" data-toggle="tooltip">
                            <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key].value == 1}ok.gif{else}no_ok.gif{/if}"  />
            </span>



            {elseif $tr[$key].value == 2}
                <span class="comments-suggest-to-delete label-tooltip" data-original-title="{l s='The customer has disabled and blog posts is hidden' mod='blockblog'}" data-toggle="tooltip"><i class="fa fa-remove fa-lg fa-3x"></i></span>
            {/if}

        {elseif isset($params.type_custom) && $params.type_custom == 'is_show'}

            {if $tr[$key].value == 1 || $tr[$key].value == 0}

                <span class="comments-suggest-to-delete label-tooltip" data-original-title="{if $tr[$key].value == 0}{l s='The customer profile is not displayed on the site' mod='blockblog'}{else}{l s='The customer profile is displayed on the site' mod='blockblog'}{/if}" data-toggle="tooltip">
                            <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key].value == 1}ok.gif{else}no_ok.gif{/if}"  />
            </span>

            {/if}

        {elseif isset($params.type_custom) && $params.type_custom == 'count_posts'}


            <span class="label-tooltip" data-original-title="{l s='Total' mod='blockblog'}" data-toggle="tooltip">
                    <span class="badge">
                        {$tr['count_posts']|escape:'htmlall':'UTF-8'}
                     </span>
                </span>

            <span style="font-size:14px;font-weight:bold">
                    (
                    <span class="label-tooltip" data-original-title="{l s='Enabled' mod='blockblog'}" data-toggle="tooltip">

                    <span style="color:green">{$tr['count_posts_active']|escape:'htmlall':'UTF-8'}</span>
                        </span>
                    /
                    <span class="label-tooltip" data-original-title="{l s='Disabled' mod='blockblog'}" data-toggle="tooltip">
                 <span style="color:red">{$tr['count_posts_noactive']|escape:'htmlall':'UTF-8'}</span>
                        </span>
                    )
                <span>


        {elseif isset($params.type_custom) && $params.type_custom == 'count_comments'}


            <span class="label-tooltip" data-original-title="{l s='Total' mod='blockblog'}" data-toggle="tooltip">
                    <span class="badge">
                        {$tr['count_comments']|escape:'htmlall':'UTF-8'}
                     </span>
                </span>

            <span style="font-size:14px;font-weight:bold">
                    (
                    <span class="label-tooltip" data-original-title="{l s='Enabled' mod='blockblog'}" data-toggle="tooltip">

                    <span style="color:green">{$tr['count_comments_active']|escape:'htmlall':'UTF-8'}</span>
                        </span>
                    /
                    <span class="label-tooltip" data-original-title="{l s='Disabled' mod='blockblog'}" data-toggle="tooltip">
                 <span style="color:red">{$tr['count_comments_noactive']|escape:'htmlall':'UTF-8'}</span>
                        </span>
                    )
                <span>

        {elseif isset($params.type_custom) && $params.type_custom == 'author_name_custom'}

            {if isset($tr['author_name'])}
                {$tr['author_name']|escape:'htmlall':'UTF-8'}
            {else}
                {$tr['firstname']|escape:'htmlall':'UTF-8'} {$tr['lastname']|escape:'htmlall':'UTF-8'}
            {/if}



        {elseif isset($params.type_custom) && $params.type_custom == 'avatar'}
            <span class="avatar-list">

             {if $tr['id_customer'] != 0}
            {* for registered customers *}
            {if strlen($tr['avatar_thumb'])>0}
                <img src="{$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{$params.path_img_cloud|escape:'htmlall':'UTF-8'}{$tr['avatar_thumb']|escape:'htmlall':'UTF-8'}" />
                {else}
                    <img src = "../modules/blockblog/views/img/avatar_m.gif" />
            {/if}
                 {* for registered customers *}

             {else}
                 {* for guests *}
                 <span {if $params.ava_on}class="label-tooltip" data-original-title="{l s='This is customer is GUEST' mod='blockblog'}" data-toggle="tooltip"{/if}>
                    <img src="../modules/blockblog/views/img/logo_comments.png" />
                 </span>
            {* for guests *}
        {/if}



        {else}
            {$smarty.block.parent}
        {/if}


    {/block}