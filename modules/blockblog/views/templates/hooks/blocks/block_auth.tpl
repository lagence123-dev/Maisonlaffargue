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

{if $blockblogava_on == 1 && count($blockblogcustomers_block)>0}

        {if $blockblogalias == "footer"}

            {if $blockblogis17 == 1}
                <div class="col-xs-12 col-sm-3 wrapper links block-authors17 {if $blockblogsr_slideru == 1 && (count($blockblogcustomers_block) > $blockblogsr_slu)}owl_users_type_carousel_blockblog{/if}">
            {else}
                <section class="footer-block col-xs-12 col-sm-3 {if $blockblogsr_slideru == 1 && (count($blockblogcustomers_block) > $blockblogsr_slu)}owl_users_type_carousel_blockblog{/if}">
            {/if}


        {else}
            <div id="blockblog_block_{$blockblogalias|escape:'htmlall':'UTF-8'}_users"
                 class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
                            {if $blockblogsr_slideru == 1 && (count($blockblogcustomers_block) > $blockblogsr_slu)}owl_users_type_carousel_blockblog{/if}
                            blockmanufacturer16 {if $blockblogis17 == 1}block-categories{/if}
                            ">
        {/if}
        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if} {if $blockblogis17 == 1 && $blockblogalias == "footer"}h3 hidden-sm-down{/if}">
            {if $blockblogalias != "footer"}<a href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}"
                    >{/if}{l s='Blog Top Authors' mod='blockblog'}{if $blockblogalias != "footer"}</a>{/if}

        </h4>

        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div data-toggle="collapse" data-target="#all_users_block_footer" class="title clearfix hidden-md-up">
                    <span class="h3">{l s='Blog Top Authors' mod='blockblog'}</span>
                                <span class="pull-xs-right">
                                  <span class="navbar-toggler collapse-icons">
                                    <i class="material-icons add">&#xE313;</i>
                                    <i class="material-icons remove">&#xE316;</i>
                                  </span>
                                </span>
                </div>
            {/if}
        {/if}

        <div class="block_content {if $blockblogalias == "footer"}block-items-data toggle-footer {if $blockblogis17 == 1}collapse{/if}{/if}" {if $blockblogalias == "footer"}{if $blockblogis17 == 1}id="all_users_block_footer"{/if}{/if}>
            {if count($blockblogcustomers_block)>0}
                <ul class="users-block-items {if $blockblogsr_slideru == 1 && (count($blockblogcustomers_block) > $blockblogsr_slu)}owl-carousel owl-theme{/if}">


                    {foreach from=$blockblogcustomers_block item=customer name=myLoop}


                        {if $blockblogsr_slideru == 1}

                            {if ($smarty.foreach.myLoop.index % $blockblogsr_slu == 0) || $smarty.foreach.myLoop.first}
                                <div>
                            {/if}

                        {/if}


                        <li>
                            <img src="{$customer.avatar_thumb|escape:'htmlall':'UTF-8'}"
                                 class="user-img-blockblog"
                                 title="{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                 alt = "{$customer.customer_name|escape:'htmlall':'UTF-8'}" />

                            <div class="float-left">
                                <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                   title="{$customer.customer_name|escape:'htmlall':'UTF-8'}">
                                    {$customer.customer_name|escape:'htmlall':'UTF-8'}
                                </a>
                                <br/>
                                <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                   title="{l s='View all posts by' mod='blockblog'} {$customer.customer_name|escape:'htmlall':'UTF-8'}"
                                   class="author_count_posts">
                                    {l s='View' mod='blockblog'} {$customer.count_posts|escape:'htmlall':'UTF-8'} {l s='posts' mod='blockblog'}
                                </a>
                            </div>
                            <div class="clr"></div>
                        </li>


                        {if $blockblogsr_slideru == 1}

                            {if ($smarty.foreach.myLoop.index % $blockblogsr_slu == $blockblogsr_slu - 1) || $smarty.foreach.myLoop.last}
                                </div>
                            {/if}

                        {/if}


                    {/foreach}
                </ul>
            {else}
                <div class="padding-10">
                    {l s='There are not authors yet.' mod='blockblog'}
                </div>
            {/if}


            <p class="block-view-all">
                <a class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                   href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}" title="{l s='View all authors' mod='blockblog'}">
                    <span>{l s='View all authors' mod='blockblog'}</span>
                </a>
            </p>


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