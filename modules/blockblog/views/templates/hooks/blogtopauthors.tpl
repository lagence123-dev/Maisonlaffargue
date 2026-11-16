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

{if count($blockblogcustomers_block)>0}

    <div id="blockblog_block_left_users"
         class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
                    blockmanufacturer16 {if $blockblogis17 == 1}block-categories{/if}
                    ">
        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">
            <a href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}"
                    >{l s='Blog Top Authors' mod='blockblog'}</a>

        </h4>
        <div class="block_content">
            {if count($blockblogcustomers_block)>0}
                <ul class="users-block-items">


                    {foreach from=$blockblogcustomers_block item=customer name=myLoop}





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
    </div>

{/if}