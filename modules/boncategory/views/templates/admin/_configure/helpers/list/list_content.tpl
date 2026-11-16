{*
 * 2015-2021 Bonpresta
 *
 * Bonpresta Column Category
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 *  @author    Bonpresta
 *  @copyright 2015-2021 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}

{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}

    {if isset($params.type) && $params.type == 'block_image'}
        {assign var='link_rewrite' value=Category::getLinkRewrite($tr.id_category, $id_lang)}
        {if $ps_version >= 1.7}
            <img src="{$base_dir|escape:'htmlall':'UTF-8'}{$link->getCatImageLink($link_rewrite, $tr.id_category)|escape:'html':'UTF-8'}" class="img-thumbnail img-thumbnail-category"/>
        {else}
            <img src="{$base_dir|escape:'htmlall':'UTF-8'}{$link->getCatImageLink($link_rewrite, $tr.id_category, 'category_default')|escape:'html':'UTF-8'}" class="img-thumbnail img-thumbnail-category"/>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}