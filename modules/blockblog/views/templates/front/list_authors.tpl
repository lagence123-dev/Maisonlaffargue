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

{if count($blockblogcustomers)>0}

    {foreach from=$blockblogcustomers item=customer name=myLoop}

        <li class="img-list-user pl-animateu {$blockblogd_eff_shopu|escape:'htmlall':'UTF-8'}">

            {if $blockblogblog_layout_typeblog_authors_alias == 1}

            <div class="row-custom">
                <div class="col-sm-2-custom text-align-center">
                    <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                       title="{$customer.customer_name|escape:'htmlall':'UTF-8'}">
                        <img height="75" width="75" alt="{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                             title="{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                             src="{$customer.avatar_thumb|escape:'htmlall':'UTF-8'}"
                             {if $customer.exist_avatar == 0}class="profile-adv-user-img"{/if}>
                    </a>
                </div>
                <div class="col-sm-10-custom">

                    <div class="b-name">
                        <div>
                            <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                               title="{$customer.customer_name|escape:'htmlall':'UTF-8'}">
                                {$customer.customer_name|escape:'htmlall':'UTF-8'}
                            </a>

                        </div>

                    </div>
                    {if strlen($customer.info)>0}
                        <div class="blockblog-author-info">
                            <p>
                                {$customer.info|escape:'htmlall':'UTF-8'}
                            </p>
                        </div>
                    {/if}
                    <div class="b-name">
                        <div>
                            <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                               title="{l s='View all posts by' mod='blockblog'} {$customer.customer_name|escape:'htmlall':'UTF-8'}"
                               class="author_count_posts">
                                {l s='View' mod='blockblog'} {$customer.count_posts|escape:'htmlall':'UTF-8'} {l s='posts' mod='blockblog'}
                            </a>
                        </div>

                    </div>
                </div>
                <div class="clear"></div>
            </div>

            {else}
            <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
               title="{$customer.customer_name|escape:'htmlall':'UTF-8'}">
                <img height="75" width="75" alt="{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                     title="{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                     src="{$customer.avatar_thumb|escape:'htmlall':'UTF-8'}"
                     {if $customer.exist_avatar == 0}class="profile-adv-user-img"{/if}>
            </a>
                <div class="b-name">
                    <div>
                        <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                           title="{$customer.customer_name|escape:'htmlall':'UTF-8'}">
                            {$customer.customer_name|escape:'htmlall':'UTF-8'}
                        </a>

                    </div>

                </div>
            {if strlen($customer.info)>0}
            <div class="blockblog-author-info">
                <p>
                {$customer.info|escape:'htmlall':'UTF-8'}
                </p>
            </div>
            {/if}
            <div class="b-name">
                <div>

                    <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$customer.id_customer|escape:'htmlall':'UTF-8'}-{$customer.customer_name|escape:'htmlall':'UTF-8'}"
                       title="{l s='View all posts by' mod='blockblog'} {$customer.customer_name|escape:'htmlall':'UTF-8'}"
                       class="author_count_posts">
                        {l s='View' mod='blockblog'} {$customer.count_posts|escape:'htmlall':'UTF-8'} {l s='posts' mod='blockblog'}
                    </a>
                </div>

            </div>
            {/if}


        </li>


        {if $smarty.foreach.myLoop.last}
            <div class="clr"><!-- --></div>
        {/if}
    {/foreach}


{else}
    <div class="blockblog-not-found">
        {l s='Authors not found' mod='blockblog'}
    </div>
{/if}

{if $blockblogd_eff_shopu != "disable_all_effects"}
{literal}
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function(){
                blockblog_init_effects_author();
            });
        });
    </script>
{/literal}
{/if}