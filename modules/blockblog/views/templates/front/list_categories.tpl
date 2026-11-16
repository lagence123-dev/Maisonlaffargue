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

{foreach from=$categories item=category name=myLoop}
    <li class="pl-animate {$blockblogblog_cat_effect|escape:'htmlall':'UTF-8'}">
        <div class="top-blog">

            <h3>
                <a title="{$category.title|escape:'htmlall':'UTF-8'}"
                   href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$category.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$category.id|escape:'htmlall':'UTF-8'}
                          {/if}
                        "
                        >{$category.title|escape:'htmlall':'UTF-8'}</a>

            </h3>
        </div>

            <div class="row-custom">
                {if strlen($category.img)>0}
                    <div class="col-sm-4-custom">
                        <div class="photo-blog">
                            <img class="img-responsive" alt="{$category.title|escape:'htmlall':'UTF-8'}"
                                 src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$category.img|escape:'htmlall':'UTF-8'}">
                        </div>
                    </div>
                {/if}
                <div class="col-sm-{if strlen($category.img)>0}8{else}12{/if}-custom">
                    <div class="body-blog">
                        {$category.content|strip_tags|mb_substr:0:$blockblogblog_catl_tr nofilter}{if strlen($category.content)>$blockblogblog_catl_tr}...{/if}
                        {if strlen($blockblogtext_readmore)>0}
                        <br>
                        <a title="{$category.title|escape:'htmlall':'UTF-8'}" class="btn readmore"
                                   href="{if $blockblogurlrewrite_on == 1}
                                    {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$category.seo_url|escape:'htmlall':'UTF-8'}
                                  {else}
                                    {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$category.id|escape:'htmlall':'UTF-8'}
                                  {/if}
                                     ">{$blockblogtext_readmore nofilter}</a>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="clear"></div>

        <div class="top-blog">


            {if $blockblogcat_list_display_date == 1}
                <p class="float-left">

                    <time datetime="{$category.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}" pubdate="pubdate"
                            ><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$category.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</time>

                </p>
            {/if}
            <p class="float-right comment">
                <i class="fa fa-list-alt fa-lg"></i>&nbsp; <a href="{if $blockblogurlrewrite_on == 1}
                                                                        {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$category.seo_url|escape:'htmlall':'UTF-8'}
                                                                      {else}
                                                                        {$blockblogcategory_url|escape:'htmlall':'UTF-8'}{$category.id|escape:'htmlall':'UTF-8'}
                                                                      {/if}"
                                                              title="{$category.title|escape:'htmlall':'UTF-8'}">{$category.count_posts|escape:'htmlall':'UTF-8'} {l s='posts' mod='blockblog'}</a>
            </p>

            <div class="clear"></div>
        </div>


    </li>
{/foreach}




{if $blockblogblog_cat_effect != "disable_all_effects"}
{literal}
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function(){
                blockblog_init_effects();
            });
        });
    </script>
{/literal}
{/if}