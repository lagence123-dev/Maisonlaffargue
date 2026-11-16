{*
* addcatdesc module - template file for hooks.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}

<div class="addcatdesc_faq_wrapper" itemscope="" itemtype="https://schema.org/FAQPage">
        <h2>{$category_name|escape:'htmlall':'UTF-8'} - {l s='Questions and answers' mod='addcatdesc'}</h2>
        <div class="faq_item_wrapper">
            {foreach from=$faq_result item=qa_item name=qa_item_name}
            <label class="faq_item_label">
                <input type="checkbox" class="faq_item_checkbox" >
                 <div class="faq_item" itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question">
                     <div class="faq_qstn" itemprop="name">
                        <span>
                            {$qa_item['question']|escape:'htmlall':'UTF-8'} 
                        </span>
                        <svg class="faq_svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" /></svg>
                     </div>
                     <div class="faq_answer_wrapper" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <div class="faq_answer" itemprop="text">
                            {$qa_item['answer'] nofilter}
                        </div>
                     </div>
                 </div>
             </label>
             {/foreach}
             
         </div>   
</div>