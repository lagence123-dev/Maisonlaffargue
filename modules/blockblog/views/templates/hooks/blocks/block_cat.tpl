{*
**
 * 2011 - 2020 StorePrestaModules SPM LLC.
 *
 * MODULE blockblog
 *
 * @author    SPM <spm.presto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   2.4.3
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=790166
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 *
*}

{if $blockblogisdisablebl == 0 || count($blockblogcategories) > 0}

    {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            <div class="col-xs-12 col-sm-3 wrapper links {if $blockblogbcat_slider == 1}owl_blog_cat_type_carousel{/if}">
        {else}
            <section class="blockblogcat_block_footer footer-block col-xs-12 col-sm-3 {if $blockblogbcat_slider == 1}owl_blog_cat_type_carousel{/if}">
        {/if}
    {else}
        <div id="blockblogcat_block_{$blockblogalias|escape:'htmlall':'UTF-8'}"
             class="block
             {if $blockblogbcat_slider == 1}owl_blog_cat_type_carousel{/if}
             margin-top-10
             {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
             {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
    {/if}

        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if} {if $blockblogis17 == 1 && $blockblogalias == "footer"}h3 hidden-sm-down{/if}" >
            {l s='Blog Categories' mod='blockblog'}
        </h4>

        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div data-toggle="collapse" data-target="#blockblogcat_block_footer" class="title clearfix hidden-md-up">
                    <span class="h3">{l s='Blog Categories' mod='blockblog'}</span>
                            <span class="pull-xs-right">
                              <span class="navbar-toggler collapse-icons">
                                <i class="material-icons add">&#xE313;</i>
                                <i class="material-icons remove">&#xE316;</i>
                              </span>
                            </span>
                </div>
            {/if}
        {/if}

        <div class="block_content {if $blockblogalias == "footer"}block-items-data toggle-footer {if $blockblogis17 == 1}collapse{/if}{/if}" {if $blockblogalias == "footer"}{if $blockblogis17 == 1}id="blockblogcat_block_footer"{/if}{/if}>

            {if count($blockblogcategories) > 0}



                <div class="items-cat-block">

                    {if $blockblogbcat_slider == 1 && (count($blockblogcategories) > $blockblogbcat_sl)}<ul class="owl-carousel owl-theme">{/if}

                        {foreach from=$blockblogcategories item=items name=myLoop1}
                            {foreach from=$items.data item=blog name=myLoop}

                                {if $blockblogbcat_slider == 1}

                                    {if ($smarty.foreach.myLoop1.index % $blockblogbcat_sl == 0) || $smarty.foreach.myLoop1.first}
                                        <div>
                                    {/if}

                                {/if}

                                <div class="name-category">
                                    <a title="{$blog.title|escape:'htmlall':'UTF-8'}"
                                       href="{if $blockblogurlrewrite_on == 1}{$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}{/if}"
                                            >{$blog.title|escape:'htmlall':'UTF-8'} ({$blog.count_posts|escape:'htmlall':'UTF-8'})</a>
                                </div>

                                {if $blockblogbcat_slider == 1}

                                    {if ($smarty.foreach.myLoop1.index % $blockblogbcat_sl == $blockblogbcat_sl - 1) || $smarty.foreach.myLoop1.last}
                                        </div>
                                    {/if}
                                {/if}


                            {/foreach}
                        {/foreach}

                        {if $blockblogbcat_slider == 1 && (count($blockblogcategories) > $blockblogbcat_sl)}</ul>{/if}

                    <p class="block-view-all category-button-view-all">
                        <a title="{l s='View all categories' mod='blockblog'}"  class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                           href="{$blockblogcategories_url|escape:'htmlall':'UTF-8'}"><b>{l s='View all categories' mod='blockblog'}</b></a>
                    </p>
                </div>




            {else}
                <div class="block-no-items">
                    {l s='There are not categories yet.' mod='blockblog'}
                </div>
            {/if}
        </div>
    {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            </div>
        {else}
            </section>
        {/if}
    {else}
        </div>
    {/if}


{/if}

