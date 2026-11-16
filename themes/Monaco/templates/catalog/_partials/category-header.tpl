{**
* 2007-2020 PrestaShop.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2020 PrestaShop SA
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}

<div id="js-product-list-header" class="container-category mx-auto py-4">
    <div class="category-header row px-md-0 px-4">
        {if isset($category.image) && !empty($category.image.large.url)}
            <div class="col-lg-6 col-md-6 pr-lg-4 col-sm-12 pb-2 pb-md-0 category-cover d-block">
                <img class="img-fluid w-100"
                    src="{$category.image.large.url}"
                    alt="{if !empty($category.image.legend)}{$category.image.legend}{else}{$category.name}{/if}"
                    loading="lazy">
            </div>
        {/if}
        <div class="{if isset($category.image) && !empty($category.image.large.url)}col-lg-6 col-md-6 pl-lg-4 col-sm-12{else}col-12{/if} d-flex justify-content-center flex-column">
            {if $listing.pagination.items_shown_from == 1}
                {block name='breadcrumb'}
                    {include file='_partials/breadcrumb.tpl'}
                {/block}
                <h1 class="h1">{$category.name}</h1>
                {if isset($category.description) && !empty($category.description) || isset($category.image) && !empty($category.image.large.url)}
                    <div class="block-category">
                        <div class="block-category-inner row">
                            {if $category.description && !empty($category.description)}
                                <div id="category-description" class="text-muted">{$category.description nofilter}</div>
                            {/if}
                        </div>
                    </div>
                {/if}
                {block name='subcategory_list'}
                    {if isset($subcategories) && $subcategories|@count > 0}
                        {include file='catalog/_partials/subcategories.tpl' subcategories=$subcategories}
                    {/if}
                {/block}
            {/if}
        </div>
    </div>
</div>