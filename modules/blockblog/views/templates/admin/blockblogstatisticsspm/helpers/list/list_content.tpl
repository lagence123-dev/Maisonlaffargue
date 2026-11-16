{*
/**
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
 */
*}

{extends file="helpers/list/list_content.tpl"}
    {block name="td_content"}


        {if isset($params.type_custom) && $params.type_custom == 'total_items'}

            {if isset($tr.total_money)}
                <span class="label-tooltip" data-original-title="{l s='Valid orders placed / Total money spent since registration' mod='blockblog'}" data-toggle="tooltip">

                <span class="badge">
                    {$tr.total_orders|escape:'htmlall':'UTF-8'}
                </span>
                    /
                <span class="badge badge-success">
                    {if $params.is_16}
                        {displayWtPriceWithCurrency price=$tr.total_money currency=$currency}
                    {else}
                        {displayWtPriceWithCurrency price=$tr.total_money currency=0}
                    {/if}

                </span>
                </span>
            {else}
                --
            {/if}

        {elseif isset($params.type_custom) && $params.type_custom == 'total_items_loyalty'}

            {if isset($tr.count_points)}
                <span class="label-tooltip" data-original-title="{l s='Total Loyalty points / Used Loyalty points / Not used Loyalty points' mod='blockblog'}" data-toggle="tooltip">


                        {if $tr.count_points != 0}
                        <span class="badge">
                            {$tr.count_points|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}

                        /

                        {if $tr.used_points != 0}
                        <span class="badge badge-success">
                            {$tr.used_points|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}

                        /

                        {if $tr.not_used_points != 0}
                        <span class="badge badge-warning">
                            {$tr.not_used_points|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}



                </span>
            {else}
                --
            {/if}


        {elseif isset($params.type_custom) && $params.type_custom == 'total_items_blog_posts'}

            {if isset($tr.count_points)}
                <span class="label-tooltip" data-original-title="{l s='Total Blog posts / Active Blog posts  / Not active Blog posts ' mod='blockblog'}" data-toggle="tooltip">


                        {if $tr.count_posts != 0}
                        <span class="badge">
                            {$tr.count_posts|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}

                        /

                        {if $tr.count_posts_active != 0}
                        <span class="badge badge-success">
                            {$tr.count_posts_active|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}

                        /

                        {if $tr.count_posts_noactive != 0}
                        <span class="badge badge-warning">
                            {$tr.count_posts_noactive|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}



                </span>
            {else}
                --
            {/if}


        {elseif isset($params.type_custom) && $params.type_custom == 'total_items_blog_comments'}

            {if isset($tr.count_points)}
                <span class="label-tooltip" data-original-title="{l s='Total Blog Comments / Active Blog Comments / Not active Blog Comments' mod='blockblog'}" data-toggle="tooltip">


                        {if $tr.count_comments != 0}
                        <span class="badge">
                            {$tr.count_comments|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}

                        /

                        {if $tr.count_comments_active != 0}
                        <span class="badge badge-success">
                            {$tr.count_comments_active|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}

                        /

                        {if $tr.count_comments_noactive != 0}
                        <span class="badge badge-warning">
                            {$tr.count_comments_noactive|escape:'htmlall':'UTF-8'}
                        </span>
                        {else}
                            0
                        {/if}



                </span>
            {else}
                --
            {/if}


        {else}
            {$smarty.block.parent}
        {/if}


    {/block}