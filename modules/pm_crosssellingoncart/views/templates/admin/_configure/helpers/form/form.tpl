{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type == 'advancedstyles'}
        <fieldset id="csoc_advanced_styles_fieldset">
            <div class="dynamicTextarea" style="width:95%;">
                <textarea style="height:150px" rows="5" name="ADVANCED_STYLES" id="CSOC_css">{$fields_value[$input.name]}{* HTML *}</textarea>
            </div>

            <div class="clear"></div>

            <script type="text/javascript">
                var editorCSOC_css = CodeMirror.fromTextArea(document.getElementById("CSOC_css"), {ldelim}mode: "css", lineNumbers: true, autofocus: false{rdelim});
            </script>
        </fieldset>

    {elseif $input.type == 'title'}
        <h2>{$input.content}</h2>{* HTML *}

    {elseif $input.type == 'html'}
        {$input.html_content}{* HTML *}

    {elseif $input.type == 'produitsimposes'}
        <script>var idShop = {$id_shop|intval}</script>
            <div class="form-group {$input.form_group_class|escape:'html':'UTF-8'}" id="csoc_compulsory_products_form_group">
                <div class="col-lg-12">
                    <input type="hidden" name="PM_CSOC_inputProducts" id="PM_CSOC_inputProducts" value="{foreach $input.values as $product}{$product.id|intval}-{/foreach}" />
                    <input type="hidden" name="PM_CSOC_nameProducts" id="PM_CSOC_nameProducts" value="{foreach $input.values as $product}{$product.name|escape:'html':'UTF-8'}¤{/foreach}" />
                    <div id="ajax_choose_product">
                        <div class="input-group">
                            <input type="text" id="product_autocomplete_input" name="product_autocomplete_input" placeholder="{l s='Start typing an ID, reference, or product name' mod='pm_crosssellingoncart'}" />
                            <span class="input-group-addon"><i class="icon-search"></i></span>
                        </div>
                    </div>

                    <div id="PM_CSOC_divProducts">
                        {foreach $input.values as $product}
                            <div id="csoc_product_{$product.id|intval}" class="form-control-static">
                                <button type="button" class="btn btn-default" name="{$product.id|intval}" onclick="delProduct({$product.id|intval})">
                                    <i class="icon-remove text-danger"></i>
                                </button>
                                <img src="{$product.image_link|escape:'html':'UTF-8'}" />{$product.name|escape:'html':'UTF-8'}
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
