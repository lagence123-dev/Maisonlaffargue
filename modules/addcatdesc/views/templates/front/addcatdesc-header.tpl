{*
* addcatdesc module - template file for hooks.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}
<script>
    {if $partial_number > 0}
        var moreTxt = "{l s='Show more...' mod='addcatdesc'}";
        var lessTxt = "{l s='Show less...' mod='addcatdesc'}";
    {/if}
    {if $show_fp > 0}
        var showFp = 1;
        var ajax_addcatdesc_link = "{$ajax_link|escape:'html':'UTF-8'}&action={$ajax_action|escape:'html':'UTF-8'}&id_category={$ajax_category|escape:'html':'UTF-8'}&desc=1";
    {else}
        var showFp = 0;
    {/if}
    {if $show_faq_fp > 0}
        var showFpFaq = 1;
        var ajax_addcatdesc_faq_link = "{$ajax_link|escape:'html':'UTF-8'}&action={$ajax_action|escape:'html':'UTF-8'}&id_category={$ajax_category|escape:'html':'UTF-8'}&faq=1";    
    {else}
        var showFpFaq = 0;
    {/if}
</script>
{if $partial_number > 0}
<style>
.addcatdesc_wrapper::after {
    background: linear-gradient(to bottom,rgba({$partial_color|escape:'html':'UTF-8'},0) 0%,rgba({$partial_color|escape:'html':'UTF-8'},.85) 71%,rgba({$partial_color|escape:'html':'UTF-8'},1) 100%);
}
{if $partial_height > 0}
.addcatdesc_wrapper {
    max-height: {$partial_height|intval}px;
}    
{/if}
{if $partial_position > 0}
.addcatdesc_button_wrapper .but_desc_manage {
    text-align: {if $partial_position == 1}center{elseif $partial_position == 2}right{else}left{/if};
}
{/if}
</style>
{/if}