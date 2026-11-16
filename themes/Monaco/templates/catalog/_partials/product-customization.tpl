{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2020 PrestaShop SA
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}

{if !$form}
  <section class="product-customization">
      {if !$configuration.is_catalog}
          <div class="card card-body" id="customizationSection"{if !isset($product.attributes[14]) || (isset($product.attributes[14]) && isset($product.attributes[14]['id_attribute']) && $product.attributes[14]['id_attribute'] != 608)} style="display: none;"{/if}>
              <p class="h4 card-title">Taille personnalisée (en cm)</p>
              {block name='product_customization_form'}
                <div id="product-customization-form-div" data-action="{$product.url}">
                  <ul class="clearfix">
                    {foreach from=$customizations.fields item="field"}
                      <li class="product-customization-item">
                        <label> {$field.label}</label>
                        {if $field.type == 'text'}
                          <textarea placeholder="{l s='Your message here' d='Shop.Forms.Help'}" class="product-message" maxlength="250" {if $field.required} required {/if} name="{$field.input_name}"></textarea>
                          <small class="float-xs-right">{l s='250 char. max' d='Shop.Forms.Help'}</small>
                          {if $field.text !== ''}
                            <h6 class="customization-message">{l s='Your customization:' d='Shop.Theme.Catalog'}
                              <label>{$field.text}</label>
                            </h6>
                          {/if}
                        {elseif $field.type == 'image'}
                          {if $field.is_customized}
                            <br>
                            <img src="{$field.image.small.url}">
                            <a class="remove-image" href="{$field.remove_image_url}" rel="nofollow">{l s='Remove Image' d='Shop.Theme.Actions'}</a>
                          {/if}
                          <span class="custom-file">
                            <span class="js-file-name">{l s='No selected file' d='Shop.Forms.Help'}</span>
                            <input class="file-input js-file-input" {if $field.required} required {/if} type="file" name="{$field.input_name}">
                            <button class="btn btn-primary">{l s='Choose file' d='Shop.Theme.Actions'}</button>
                          </span>
                          <small class="float-xs-right">{l s='.png .jpg .gif' d='Shop.Forms.Help'}</small>
                        {/if}
                      </li>
                    {/foreach}
                  </ul>
                  <div class="clearfix">
                    <button class="btn btn-primary float-xs-right" type="button">{l s='Save Customization' d='Shop.Theme.Actions'}</button>
                  </div>
                </div>
              {/block}
          </div>
          <br><br>
          <h3>Comment mesurer votre ceinture pour un ajustement parfait</h3>
          <ul class="size-loop__guide">
            <li>Prenez votre ceinture actuelle et étendez-la à plat.</li>
            <li>Mesurez depuis le bout de l'ardillon (la tige métallique de la boucle) jusqu'au trou que vous utilisez habituellement. (Référez-vous au schéma pour visualiser précisément où mesurer.)</li>
            <li>Notez la mesure en centimètres.</li>
            <li>Lors de votre commande, indiquez cette mesure exacte pour assurer un ajustement parfait.</li>
          </ul>
          <img class="img-fluid" src="https://www.maisonlaffargue.fr/img/cms/ceinture-guide.png">
      {/if}
  </section>
{else}
  <form id="product-customization-form" class="invisible d-none" method="post" action="{$product.url}" enctype="multipart/form-data">
    <ul class="clearfix">
      {foreach from=$customizations.fields item="field"}
        <li class="product-customization-item">
          <label> {$field.label}</label>
          {if $field.type == 'text'}
            <textarea placeholder="{l s='Your message here' d='Shop.Forms.Help'}" class="product-message" maxlength="250" {if $field.required} required {/if} name="{$field.input_name}"></textarea>
            <small class="float-xs-right">{l s='250 char. max' d='Shop.Forms.Help'}</small>
            {if $field.text !== ''}
              <h6 class="customization-message">{l s='Your customization:' d='Shop.Theme.Catalog'}
                <label>{$field.text}</label>
              </h6>
            {/if}
          {elseif $field.type == 'image'}
            {if $field.is_customized}
              <br>
              <img src="{$field.image.small.url}">
              <a class="remove-image" href="{$field.remove_image_url}" rel="nofollow">{l s='Remove Image' d='Shop.Theme.Actions'}</a>
            {/if}
            <span class="custom-file">
              <span class="js-file-name">{l s='No selected file' d='Shop.Forms.Help'}</span>
              <input class="file-input js-file-input" {if $field.required} required {/if} type="file" name="{$field.input_name}">
              <button class="btn btn-primary">{l s='Choose file' d='Shop.Theme.Actions'}</button>
            </span>
            <small class="float-xs-right">{l s='.png .jpg .gif' d='Shop.Forms.Help'}</small>
          {/if}
        </li>
      {/foreach}
    </ul>
    <div class="clearfix">
      <button class="btn btn-primary float-xs-right" type="submit" name="submitCustomizedData">{l s='Save Customization' d='Shop.Theme.Actions'}</button>
    </div>
  </form>
{/if}