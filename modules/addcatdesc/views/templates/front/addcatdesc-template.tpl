{*
* addcatdesc module - template file for hooks.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}
{if isset($add_category_description) && !empty($add_category_description)}
    <div id="addcatdesc_wrapper">
        {if !isset($add_cat_desc_wrap_set)}
            {include file='module:addcatdesc/views/templates/front/addcatdesc-template-content.tpl'}
        {/if}
	</div>
{/if}

{if isset($faq_result) && count($faq_result) > 0}
    <div id="addcatdesc_wrapper_faq">
        {if !isset($add_cat_desc_wrap_faq_set)}
            {include file='module:addcatdesc/views/templates/front/addcatdesc-template-faq-content.tpl'}
        {/if}
    </div>
{/if}