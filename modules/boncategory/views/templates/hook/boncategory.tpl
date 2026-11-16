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

{function name="subcategories" nodes=[] depth=0}
    {if ($nodes)}
        {if Category::getChildren($nodes, Context::getContext()->language->id)}
            {assign var='childCategories' value=Category::getChildren($nodes, Context::getContext()->language->id)}
            <ul data-bonsubcategories="{$nodes + 1}">
                {foreach from=$childCategories item=subcategory}
                    <li>
                        <a href="{$link->getCategoryLink($subcategory.id_category|escape:'htmlall':'UTF-8')}">{$subcategory.name}</a>
                        {subcategories nodes=$subcategory.id_category}
                    </li>
                {/foreach}
            </ul>
        {/if}
    {/if}
{/function}
{if isset($categories) && $categories}
    <div id="{if $category_hover}boncategory-overlay{/if}"></div>
    <section id="boncategory">
        <div class="boncategory-title" style="background-color: {$category_header}">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#custom-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span style="background: {$category_header_color}" class="icon-bar"></span>
                <span style="background: {$category_header_color}" class="icon-bar"></span>
                <span style="background: {$category_header_color}" class="icon-bar"></span>
            </button>
            <h4 style="color: {$category_header_color}">{l s='browse categories' mod='boncategory'}</h4>
        </div>
        <ul class="sf-menu sf-vertical">
            {foreach from=$categories item=category name=category}
                <li data-boncategories="{$category.category->id_category}" class="{if Category::getChildren($category.category->id_category, Context::getContext()->language->id)}submain{/if}">
                    <div>
                        <h5>
                           <a href="{$link->getCategoryLink($category.category->id_category|escape:'htmlall':'UTF-8')}" title="{$category.category->name|escape:'htmlall':'UTF-8'}">
                               {if isset($category.icon) && $category.icon && $category.switch_icon} <i class="{$category.icon|escape:'htmlall':'UTF-8'}">{if $category.type_icon=='material_icons'}{$category.icon|escape:'htmlall':'UTF-8'|replace:'material-icons':''}{/if}</i> {/if}
                               {$category.category->name|escape:'htmlall':'UTF-8'}
                           </a>
                        </h5>
                    </div>
                    {if $category.content}
                        <div>
                            {$category.content nofilter}
                        </div>
                    {/if}
                    {if Category::getChildren($category.category->id_category, Context::getContext()->language->id)}
                        {assign var='childCategories' value=Category::getChildren($category.category->id_category, Context::getContext()->language->id)}
                        <ul data-bonsubcategories="{$childCategories[0].id_category}">
                            {foreach from=$childCategories item=subcategory}
                                <li>
                                    <a href="{$link->getCategoryLink($subcategory.id_category|escape:'htmlall':'UTF-8')}">{$subcategory.name}</a>
                                    {subcategories nodes=$subcategory.id_category}
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </li>
            {/foreach}
        </ul>
    </section>
{/if}