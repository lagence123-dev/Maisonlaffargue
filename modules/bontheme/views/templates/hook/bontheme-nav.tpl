{*
* 2015-2020 Bonpresta
*
* Bonpresta Theme
*
* NOTICE OF LICENSE
*
* This source file is subject to the General Public License (GPL 2.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/GPL-2.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future.
*
* @author Bonpresta
* @copyright 2015-2020 Bonpresta
* @license http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}
<div class="bon-custom-menu-nav">
    {if isset($theme_enable_setting) && $theme_enable_setting}
        <div id="custom-menu-open-nav"></div>
    {/if}
    <div class="bon-custom-menu-popup">
        <div class="boxed-setting">
            <p>Boxed:</p>
            <span class="toggle-bg ">
            <input class="input-boxed " type="radio" value="on">
            <span class="switch-boxed "></span>
        </span>
        </div>
        <div class="Sticky-header">
            <p>Sticky Header:</p>
            <span class="toggle-bg active">
            <input class="input-sticky-header" type="radio" value="on">
            <span class="switch-header active"></span>
        </span>
        </div>
        <div class="sticky-addcart">
            <p>Sticky Add To Cart</p>
            <span class="toggle-bg active">
            <input class="input-sticky-cart" type="radio" value="on">
            <span class="switch-cart active"></span>
        </span>
        </div>
        <div class="Sticky-footer">
            <p>Sticky Footer:</p>
            <span class="toggle-bg active">
            <input class="input-sticky-footer" type="radio" value="on">
            <span class="switch-footer active"></span>
        </span>
        </div>
        <div class="bon-select-language">
            <p>Font:</p>
            <form id="bon-select">
                <select class="bon-select-form" name="language-select" form="bon-select">
                    <option value="Lato">Lato</option>
                    <option value="Raleway">Raleway</option>
                    <option value="OpenSans">OpenSans</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Oswald">Oswald</option>
                    <option value="Ubuntu">Ubuntu</option>
                    <option value="Playfair">Playfair</option>
                    <option value="Lora">Lora</option>
                    <option value="Indie">Indie</option>
                    <option value="Hind">Hind</option>
                </select>
            </form>
        </div>
    </div>

</div>