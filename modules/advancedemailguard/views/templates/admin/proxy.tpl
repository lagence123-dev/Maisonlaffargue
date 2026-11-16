{**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2026 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}
<iframe src="{$reduxAdminLink|escape:'html':'UTF-8'}" frameborder="0" id="reduxWebAdmin" width="100%" height="500px" data-css="{$reduxAdminCss|escape:'html':'UTF-8'}"></iframe>
<a href="{$reduxAdminLink|escape:'html':'UTF-8'}" id="reduxWebAdminLink" style="display: none;">{l s='Full page' mod='advancedemailguard'} ⤢</a>
{literal}
<script>
(function () {
    'use strict';

    document.body.style.overflow = 'hidden';
    var iframe = document.getElementById('reduxWebAdmin');

    function adjustHeight() {
        iframe.style.height =
        Math.abs(iframe.offsetTop - window.innerHeight) + 'px';
    }

    setTimeout(adjustHeight, 100); // Initial adjustment after DOM load
    window.addEventListener('resize', adjustHeight);

    var adminLink = document.getElementById('reduxWebAdminLink');
    adminLink.classList.add('toolbar_btn');

    var toolbarList = document.querySelector('.btn-toolbar > ul');
    var listItem = document.createElement('li');
    listItem.appendChild(adminLink);
    toolbarList.insertBefore(listItem, toolbarList.firstChild);
    adminLink.style.display = '';

    document.head.insertAdjacentHTML(
        'beforeend',
        '<link rel="stylesheet" href="' + iframe.dataset.css + '">'
    );
})();
</script>
{/literal}