{*
* addcatdesc module - template file for hooks.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}
{* 
  <div class="offset-lg-1 col-lg-10 my-55 px-7 px-lg-0">
    {if !empty($category.description|strip_tags:true|trim) && $listing.pagination.items_shown_from == 1}
      <div id="category-description-2" class="block-content-seo container text-center read_more_content">
        {$category.description nofilter}
      </div>
      <p data-more="{l s='More' d='Shop.Theme.Global'}" data-less="{l s='Less' d='Shop.Theme.Global'}" class="text-center toggle read_more_toggle p-2"></p>
    {/if} *}


{if isset($add_category_description) && !empty($add_category_description)}
    {if strlen($add_category_description) >= $partial_number && $partial_number > 0}
    	<div class="addcatdesc_button_wrapper block col-lg-7 col-12 mx-auto">
            <!-- / Additional category description hooks -->
            <div class="addcatdesc_wrapper">
        		{$add_category_description nofilter}
        	</div>
            <div class="but_desc_manage">
                <span class="wide-button" onclick="showAddCatDesc(this);">{l s='Show more...' mod='addcatdesc'}</span>
            </div>
            <!-- /# Additional category description hooks -->
        </div>

    {else}
        <div class="addcatdesc_button_wrapper block col-lg-7 col-12 mx-auto">
            <!-- / Additional category description hooks -->
            {$add_category_description nofilter}
            <!-- /# Additional category description hooks -->
    	</div>
    {/if}
{/if}