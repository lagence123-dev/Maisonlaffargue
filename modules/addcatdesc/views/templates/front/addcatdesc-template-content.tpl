{*
* addcatdesc module - template file for hooks.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}

{if isset($add_category_description) && !empty($add_category_description)}
    {if strlen($add_category_description) >= $partial_number && $partial_number > 0}
    	<div class="addcatdesc_button_wrapper block">
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
        <div class="addcatdesc block">
            <!-- / Additional category description hooks -->
    		{$add_category_description nofilter}
            <!-- /# Additional category description hooks -->
    	</div>
    {/if}
{/if}