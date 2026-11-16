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
            {literal}
                <script type="text/javascript">

                    var ajax_link_blockblog = '{/literal}{$params.ajax_link nofilter}{literal}';

                </script>
            {/literal}

            <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}" class="{$params.type_custom|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'} gallery{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate item on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'gallery','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
                        <img src="../img/admin/../../modules/blockblog/views/img/{if $tr[$key] == 1}ok.gif{else}no_ok.gif{/if}"  />
                    </a>
                </span>
            </span>
        {elseif isset($params.type_custom) && $params.type_custom == 'is_active_featured'}
        {literal}
            <script type="text/javascript">

                var ajax_link_blockblog = '{/literal}{$params.ajax_link nofilter}{literal}';

            </script>
        {/literal}

            <span id="activeitem{$tr['id']|escape:'htmlall':'UTF-8'}" class="{$params.type_custom|escape:'htmlall':'UTF-8'}{$tr['id']|escape:'htmlall':'UTF-8'}">
                    <span class="label-tooltip" data-original-title="{l s='Click here to activate or deactivate item on your site' mod='blockblog'}" data-toggle="tooltip">
                    <a href="javascript:void(0)" onclick="blockblog_list({$tr['id']|escape:'htmlall':'UTF-8'},'active',{$tr[$key]|escape:'htmlall':'UTF-8'},'galleryfeatured','{$params.token_custom|escape:'htmlall':'UTF-8'}');" style="text-decoration:none">
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
        {else}
            {$smarty.block.parent}
        {/if}


    {/block}