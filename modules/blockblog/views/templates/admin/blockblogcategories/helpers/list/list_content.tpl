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
        {if isset($params.type_custom) && $params.type_custom == 'title_category'}
            {if isset($tr[$key])}
                <span class="label-tooltip" data-original-title="{l s='Click here to see blog category on your site' mod='blockblog'}" data-toggle="tooltip">


                    <a href="
                    {*{if $params.is_rewrite == 0}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}
                    {else}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr.seo_url|escape:'htmlall':'UTF-8'}
                    {/if}*}
                    {if $params.is_rewrite == 0}
                        {$params.item_url|escape:'htmlall':'UTF-8'}{$tr.seo_url|escape:'htmlall':'UTF-8'}
                    {else}

                        {$params.base_dir_ssl|escape:'htmlall':'UTF-8'}{if isset($params.data_shop_uris[$tr.ids_shops])}{$params.data_shop_uris[$tr.ids_shops]|escape:'htmlall':'UTF-8'}{/if}{if $params.count_all_lang > 1}{if $tr.count_cat_for_lang > 1}{$params.iso_code|escape:'htmlall':'UTF-8'}{else}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{else}{if $params.count_all_lang == 1}{else}{$tr.language|escape:'htmlall':'UTF-8'}/{/if}{/if}{$params.alias_url|escape:'htmlall':'UTF-8'}/c-{$tr['seo_url']|escape:'htmlall':'UTF-8'}
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

            <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate category on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'category','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>
        {elseif isset($params.type_custom) && $params.type_custom == 'img'}
            {if strlen($tr[$key])>0}
                <img src="{$params.logo_img_path|escape:'htmlall':'UTF-8'}{$tr[$key]|escape:'htmlall':'UTF-8'}" class="img-thumbnail" style="width: 80px"/>
            {else}
                ---
            {/if}
        {else}
            {$smarty.block.parent}
        {/if}


    {/block}