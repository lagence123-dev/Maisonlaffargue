<div class="form-group">
    <label class="control-label col-lg-3">{l s='Category' mod='pm_advancedtopmenu'}</label>
    <div class="col-lg-4">
        <select multiple="multiple" name="id_category" class="custom-selectize inline">
            <option value="" disabled>{l s='-- Choose a category --' mod='pm_advancedtopmenu'}</option>
            {foreach from=$categoryList item=category}
                <option value="{$category.id|intval}" {if $category.id == $selected}selected="selected"{/if}>{$category.breadcrumb|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
</div>
