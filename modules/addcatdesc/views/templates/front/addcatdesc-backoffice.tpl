{*
* addcatdesc module - template file for hooks.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*}
{if isset($content)}
    
    <div class="form-group row">
    
    <label {if isset($new_tpl_version) && $new_tpl_version == 1} class="form-control-label" for="additional_description_cat" {else}class="control-label col-lg-3"{/if}>
        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Invalid characters <>;=#{}' mod='addcatdesc'}">{l s='Additional Description module' mod='addcatdesc'}</span>
    </label>
    {if isset($new_tpl_version) && $new_tpl_version == 1}
        <div class="col-sm">
    {/if}
    <div {if isset($new_tpl_version) && $new_tpl_version == 1} id="additional_description_cat" class="translations tabbable" {else}class="col-lg-9" {/if}>
    {if isset($new_tpl_version) && $new_tpl_version == 1 && count($contentLang) > 1}
        <ul class="translationsLocales nav nav-pills">
        {foreach from=$content key=k item=content_text name=it_content}
            <li class="nav-item">
                <a href="#" data-locale="{$content_text['iso_code']|escape:'htmlall':'UTF-8'}" class="{if $smarty.foreach.it_content.first} active {/if}nav-link" data-toggle="tab" data-target=".translationsFields-additional_description{$k|intval}">
                    {$content_text['iso_code']|escape:'htmlall':'UTF-8'|capitalize}
                </a>
            </li>
        {/foreach}
        </ul>
    {/if}

    {if isset($new_tpl_version) && $new_tpl_version == 1} 
        <div class="translationsFields tab-content">
    {/if}
    {foreach from=$content key=k item=content_text name=it_content}
       {if isset($new_tpl_version) && $new_tpl_version == 1}
            <div data-locale="{$content_text['iso_code']|escape:'htmlall':'UTF-8'}" class="translationsFields-additional_description{$k|intval} tab-pane translation-field  translation-label-{$content_text['iso_code']|escape:'htmlall':'UTF-8'} {if $smarty.foreach.it_content.first} active {/if}">
                <textarea id="additional_description{$k|intval}" class="rte autoload_rte" name="additional_description_{$k|intval}">
                    {$content_text['content']|escape:'htmlall':'UTF-8'}
                </textarea>
            </div>
        {else}
            <div class="form-group translatable-field lang-{$k|intval}" {if $k != $default_language} style="display:none;"{/if}>
                <div class="col-lg-9">
                        <textarea id="additional_description{$k|intval}" class="rte autoload_rte" name="additional_description_{$k|intval}">
                            {$content_text['content']|escape:'htmlall':'UTF-8'}
                        </textarea>
                </div>
                {if (count($contentLang) > 1)}
                <div class="col-lg-2">
                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                        {$content_text['iso_code']|escape:'htmlall':'UTF-8'}
                    <i class="icon-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$contentLang item=content_lang}
                            <li><a href="javascript:hideOtherLanguage({$content_lang['id_lang']|intval});" tabindex="-1">{$content_lang['name']|escape:'htmlall':'UTF-8'}</a></li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        {/if}
    {/if}
    {/foreach}

    {if isset($new_tpl_version) && $new_tpl_version == 1} 
        </div>
    {/if}

    <br />
    <input type="hidden" name="additional_description_hidden" value="1"/>
    </div>
    
    {if isset($new_tpl_version) && $new_tpl_version == 1}
        </div>
        
    {/if}
    </div>
    
    {if isset($new_tpl_version) && $new_tpl_version == 1}
        <div class="form-group row">
    {/if}
    <label {if isset($new_tpl_version) && $new_tpl_version == 1} class="form-control-label"  {else} class="control-label col-lg-3" {/if}>
            {l s='Active additional description in module' mod='addcatdesc'}
    </label>
        <div {if isset($new_tpl_version) && $new_tpl_version == 1} class="col-sm" {else} class="col-lg-9" {/if}>
            <span class="switch prestashop-switch fixed-width-lg">
				<input name="active_add" id="active_add_on" value="1" {if $active_add == 1}checked="checked"{/if} type="radio">
                <label for="active_add_on">Yes</label>
                <input name="active_add" id="active_add_off" value="0" {if $active_add == 0}checked="checked"{/if} type="radio">
                <label for="active_add_off">No</label>
                <a class="slide-button btn"></a>
            </span>
            <br />
            <div><a href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=addcatdesc">{l s='Go to the settings of the Category page module with additional description' mod='addcatdesc'}</a></div>
            <br />
        </div>
     {if isset($new_tpl_version) && $new_tpl_version == 1}
        </div>
    {/if}   
{/if}											