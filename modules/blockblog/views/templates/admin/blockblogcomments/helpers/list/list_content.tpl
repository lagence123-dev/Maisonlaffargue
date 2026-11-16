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
        {if isset($params.type_custom) && $params.type_custom == 'title_comment'}
            {if isset($tr[$key])}

                {if strlen($tr.img)>0}
                    <img src="{$params.logo_img_path|escape:'htmlall':'UTF-8'}{$tr.img|escape:'htmlall':'UTF-8'}" class="img-thumbnail" style="width: 50px;margin-right:10px"/>
                {/if}

                <span class="label-tooltip" data-original-title="{l s='Click here to see blog comment on your site' mod='blockblog'}" data-toggle="tooltip">
                    {*{$tr.id_shop}{$params.item_url|escape:'htmlall':'UTF-8'}*}
                    <a href="
                    {*{if $params.is_rewrite == 0}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}#blogcomments
                    {else}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr.seo_url|escape:'htmlall':'UTF-8'}#blogcomments
                    {/if}*}
                    {if $params.is_rewrite == 0}
                        {*{$link->getModuleLink('blockblog', {$params.alias_url|escape:'htmlall':'UTF-8'}, [], true, {$tr.id_lang|escape:'htmlall':'UTF-8'}, {$tr.id_shop|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}{if $params.is16 == 1}?{else}?{/if}post_id={$tr['id_post']|escape:'htmlall':'UTF-8'}*}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr.seo_url|escape:'htmlall':'UTF-8'}#blogcomments
                    {else}

                        {$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{$params.data_shop_uris[$tr.id_shop]|escape:'htmlall':'UTF-8'}{if $params.count_languages > 1}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{$params.alias_url|escape:'htmlall':'UTF-8'}/p-{$tr['seo_url']|escape:'htmlall':'UTF-8'}#blogcomments
                    {/if}
                    "
                       style="text-decoration:underline" target="_blank">
                        {$tr[$key]|escape:'htmlall':'UTF-8'}
                    </a>
                </span>
            {/if}

        {elseif isset($params.type_custom) && $params.type_custom == 'is_active'}
        {literal}
            <script type="text/javascript">

                var ajax_link_blockblog = '{/literal}{$params.ajax_link nofilter}{literal}';

            </script>
        {/literal}

            {if $tr[$key].value == 1 || $tr[$key].value == 0}
            <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate comment on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key].value|escape:'htmlall':'UTF-8'},'comment','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key].value == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>
            {elseif $tr[$key].value == 2}
                <span class="comments-suggest-to-delete label-tooltip" data-original-title="{l s='The customer has deleted his comment. Now you can delete comment' mod='blockblog'}" data-toggle="tooltip"><i class="fa fa-trash-o fa-lg fa-3x"></i></span>
            {elseif $tr[$key].value == 3}
                <span class="comments-suggest-to-change label-tooltip" data-original-title="{l s='The customer has changed his comment. Now you can moderate changes' mod='blockblog'}" data-toggle="tooltip"><i class="fa fa-pencil-square-o fa-lg fa-3x"></i></span>

            {/if}

        {elseif isset($params.type_custom) && $params.type_custom == 'rating'}


            <div class="rating-input">
            {if $tr['rating'] != 0}
                {for $foo=0 to 4}
                    {if $foo < $tr['rating']}
                        <i class="fa fa-star fa-lg" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>

                    {else}
                        <i class="fa fa-star-o fa-lg" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                    {/if}

                {/for}

            {else}

                {for $foo=0 to 4}
                    <i class="fa fa-star-o fa-lg" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                {/for}
            {/if}
            </div>





        {literal}
            <script type="text/javascript">
                $('document').ready( function() {
                    $('#rating-blockblog{/literal}{$tr['id']|escape:'htmlall':'UTF-8'}{literal}').val({/literal}{$tr['rating']|escape:'htmlall':'UTF-8'}{literal});
                });
            </script>
        {/literal}


        {elseif isset($params.type_custom) && $params.type_custom == 'customer_name'}
        {if isset($tr[$key])}
            {if $tr['author_id'] != 0 && $params.ava_on == 1}



                <span class="label-tooltip" data-original-title="{l s='Click here to see customer on your site' mod='blockblog'}" data-toggle="tooltip">
                        <a href="
                        {if $params.is_rewrite == 0}
                            {$params.user_url|escape:'htmlall':'UTF-8'}{$tr['author_id']|escape:'htmlall':'UTF-8'}-{$tr['author']|escape:'htmlall':'UTF-8'}
                        {else}

                            {$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{$params.data_shop_uris[$tr.id_shop]|escape:'htmlall':'UTF-8'}{if $params.count_languages > 1}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{$params.alias_url_blog|escape:'htmlall':'UTF-8'}/{$params.alias_url|escape:'htmlall':'UTF-8'}/{$tr['author_id']|escape:'htmlall':'UTF-8'}-{$tr['author']|escape:'htmlall':'UTF-8'}
                        {/if}

                        "
                           style="text-decoration:underline" target="_blank">
                            {$tr[$key]|escape:'htmlall':'UTF-8'}
                        </a>
                </span>
            {else}
                <span {if $params.ava_on}class="label-tooltip" data-original-title="{l s='This is customer is GUEST' mod='blockblog'}" data-toggle="tooltip"{/if}>
                    {$tr[$key]|escape:'htmlall':'UTF-8'}
                    </span>
            {/if}
        {/if}


        {elseif isset($params.type_custom) && $params.type_custom == 'avatar'}
        <span class="avatar-list">

             {if $tr['author_id'] != 0}
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