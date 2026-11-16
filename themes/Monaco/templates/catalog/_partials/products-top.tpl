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
<div id="js-product-list-top" class="d-flex products-selection justify-content-between">
    <div class="total-products w-50">
        <button class="btn btn-primary my-3" type="button" data-toggle="collapse" data-target="#offcanvasFilters" aria-expanded="false" aria-controls="offcanvasFilters">
            {l s='Filters' d='Shop.Theme.Actions'}
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.54658 11.77H11.7812C12.1069 12.7883 13.0634 13.53 14.19 13.53C15.3166 13.53 16.273 12.7883 16.5988 11.77H18.4534C18.8779 11.77 19.2234 11.4245 19.2234 11C19.2234 10.5754 18.8779 10.23 18.4534 10.23H16.5988C16.2731 9.21161 15.3166 8.46997 14.19 8.46997C13.0634 8.46997 12.107 9.21161 11.7812 10.23H3.54658C3.12205 10.23 2.77658 10.5754 2.77658 11C2.77658 11.4245 3.12205 11.77 3.54658 11.77ZM14.19 10.01C14.7357 10.01 15.18 10.4543 15.18 11C15.18 11.5457 14.7357 11.99 14.19 11.99C13.6443 11.99 13.2 11.5457 13.2 11C13.2 10.4543 13.6443 10.01 14.19 10.01Z" fill="white"/>
                <path d="M3.54658 6.41096H5.40118C5.72689 7.42932 6.68338 8.17096 7.81 8.17096C8.93656 8.17096 9.89303 7.42932 10.2188 6.41096H18.4534C18.8779 6.41096 19.2234 6.06549 19.2234 5.64096C19.2234 5.21644 18.8779 4.87096 18.4534 4.87096H10.2188C9.8931 3.8526 8.93662 3.11096 7.81 3.11096C6.68343 3.11096 5.72696 3.8526 5.40118 4.87096H3.54658C3.12205 4.87096 2.77658 5.21644 2.77658 5.64096C2.77658 6.06549 3.12205 6.41096 3.54658 6.41096ZM7.81 4.65096C8.35571 4.65096 8.8 5.09525 8.8 5.64096C8.8 6.18667 8.35571 6.63096 7.81 6.63096C7.26429 6.63096 6.82 6.18667 6.82 5.64096C6.82 5.09525 7.26429 4.65096 7.81 4.65096Z" fill="white"/>
                <path d="M18.4818 15.589H10.2448C9.91914 14.5707 8.96265 13.829 7.83603 13.829C6.70946 13.829 5.753 14.5707 5.42721 15.589H3.57261C3.14809 15.589 2.80261 15.9345 2.80261 16.359C2.80261 16.7836 3.14809 17.129 3.57261 17.129H5.42721C5.75292 18.1474 6.70941 18.889 7.83603 18.889C8.96259 18.889 9.91906 18.1474 10.2448 17.129H18.4818C18.9064 17.129 19.2518 16.7836 19.2518 16.359C19.2518 15.9345 18.9064 15.589 18.4818 15.589ZM7.83585 17.349C7.29014 17.349 6.84585 16.9048 6.84585 16.359C6.84585 15.8133 7.29014 15.369 7.83585 15.369C8.38156 15.369 8.82585 15.8133 8.82585 16.359C8.82585 16.9048 8.38156 17.349 7.83585 17.349Z" fill="white"/>
            </svg>
        </button>

        <div class="collapse dont-collapse-sm-up" id="offcanvasFilters">
            <div class="offcanvas-header">
                <p class="modal-title mb-0">{l s='Refine selection' d='Shop.Theme.Catalog'}</p>
                <button type="button" class="close" data-toggle="collapse" data-target="#offcanvasFilters" aria-label="Close">
                    <i class="material-icons"></i>
                </button>
            </div>
            
            <div class="offcanvas-body">
                {hook h='displayLeftColumn'}
            </div>
        </div>
        <div id="offcanvasBackdrop" class="modal-backdrop fade d-none"></div>
    </div>
    <div class="sort-by-row m-0 w-50">
        {block name='sort_by'}
            {include file='catalog/_partials/sort-orders.tpl' sort_orders=$listing.sort_orders}
        {/block}
    </div>
</div>