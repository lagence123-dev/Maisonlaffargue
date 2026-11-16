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
        {if isset($params.type_custom) && $params.type_custom == 'title_post'}
            {if isset($tr[$key])}
                <span class="label-tooltip" data-original-title="{l s='Click here to see blog post on your site' mod='blockblog'}" data-toggle="tooltip">

                    <a href="
                    {*{if $params.is_rewrite == 0}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}
                    {else}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr.seo_url|escape:'htmlall':'UTF-8'}
                    {/if}*}

                    {if $params.is_rewrite == 0}
                        {*{$link->getModuleLink('blockblog', 'blog', [], true, {$tr.id_lang|escape:'htmlall':'UTF-8'}, {$tr.id_shop|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}{if $params.is16 == 1}?{else}?{/if}post_id={$tr['id']|escape:'htmlall':'UTF-8'}*}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr.seo_url|escape:'htmlall':'UTF-8'}
                    {else}

                        {$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{if isset($params.data_shop_uris[$tr.ids_shops])}{$params.data_shop_uris[$tr.ids_shops]|escape:'htmlall':'UTF-8'}{/if}{if $params.count_all_lang > 1}{if $tr.count_posts_for_lang > 1}{$params.iso_code|escape:'htmlall':'UTF-8'}{else}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{else}{if $params.count_all_lang == 1}{else}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{/if}{$params.alias_url|escape:'htmlall':'UTF-8'}/p-{$tr['seo_url']|escape:'htmlall':'UTF-8'}
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
                <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}" class="{$params.type_custom|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate post on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key].value|escape:'htmlall':'UTF-8'},'is_active','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key].value == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
                </span>
            {elseif $tr[$key].value == 2}
                <span class="comments-suggest-to-delete label-tooltip" data-original-title="{l s='The customer has deleted his post. Now you can delete post' mod='blockblog'}" data-toggle="tooltip"><i class="fa fa-trash-o fa-lg fa-3x"></i></span>
            {elseif $tr[$key].value == 3}
                <span class="comments-suggest-to-change label-tooltip" data-original-title="{l s='The customer has changed his post. Now you can moderate changes' mod='blockblog'}" data-toggle="tooltip"><i class="fa fa-pencil-square-o fa-lg fa-3x"></i></span>
            {elseif $tr[$key].value == 4}
                <span class="comments-suggest-to-add label-tooltip" data-original-title="{l s='The customer has added new post. Now you can moderate new post' mod='blockblog'}" data-toggle="tooltip"><i class="fa fa-plus-square fa-lg fa-3x"></i></span>
            {/if}



        {elseif isset($params.type_custom) && $params.type_custom == 'is_comments'}

            <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}" class="{$params.type_custom|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate comments for post on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'is_comments','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>
            <span> ({$tr['count_comments']})</span>

        {elseif isset($params.type_custom) && $params.type_custom == 'is_fbcomments'}

            <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}" class="{$params.type_custom|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate Facebook comments for post on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'is_fbcomments','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>

        {elseif isset($params.type_custom) && $params.type_custom == 'is_slider'}

            <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}" class="{$params.type_custom|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to include or exclude post in the slider on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'is_slider','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>

        {elseif isset($params.type_custom) && $params.type_custom == 'img'}
            {if strlen($tr[$key])>0}
                {assign var=rand_blockblog value=10|mt_rand:100}
            <img src="{$params.logo_img_path|escape:'htmlall':'UTF-8'}{$tr[$key]|escape:'htmlall':'UTF-8'}?rand={$rand_blockblog|escape:'htmlall':'UTF-8'}" class="img-thumbnail" style="width: 80px"/>
            {else}
                ---
            {/if}


        {elseif isset($params.type_custom) && $params.type_custom == 'customer_name'}
            {if isset($tr[$key])}
                {if $tr['author_id'] != NULL && $params.ava_on == 1}
                    <span class="label-tooltip" data-original-title="{l s='Click here to see customer on your site' mod='blockblog'}" data-toggle="tooltip">
                        <a href="

                        {*{$params.user_url|escape:'htmlall':'UTF-8'}{$tr['author_id']|escape:'htmlall':'UTF-8'}-{$tr['author']|escape:'htmlall':'UTF-8'}*}

                        {if $params.is_rewrite == 0}
                            {$params.user_url|escape:'htmlall':'UTF-8'}{$tr['author_id']|escape:'htmlall':'UTF-8'}-{$tr['author']|escape:'htmlall':'UTF-8'}
                        {else}

                            {$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{$params.data_shop_uris[$tr.ids_shops]|escape:'htmlall':'UTF-8'}{if $params.count_all_lang > 1}{if $tr.count_posts_for_lang > 1}{$params.iso_code|escape:'htmlall':'UTF-8'}{else}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{else}{if $params.count_all_lang == 1}{else}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{/if}{$params.alias_url_blog|escape:'htmlall':'UTF-8'}/{$params.alias_url|escape:'htmlall':'UTF-8'}/{$tr['author_id']|escape:'htmlall':'UTF-8'}-{$tr['author']|escape:'htmlall':'UTF-8'}
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

             {if $tr['author_id'] != NULL}
                 {* for registered customers *}
                 {if strlen($tr['avatar_thumb'])>0}
                     {assign var=rand_blockblog value=10|mt_rand:100}
                     <img src="{$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{$params.path_img_cloud|escape:'htmlall':'UTF-8'}{$tr['avatar_thumb']|escape:'htmlall':'UTF-8'}?rand={$rand_blockblog|escape:'htmlall':'UTF-8'}" />
                {else}
                    <img src = "../modules/blockblog/views/img/avatar_m.gif" />
                 {/if}
                 {* for registered customers *}
             {else}
                {* for guests *}
                    ---
                 {* for guests *}
             {/if}
        </span>


        {else}
            {$smarty.block.parent}
        {/if}


    {/block}