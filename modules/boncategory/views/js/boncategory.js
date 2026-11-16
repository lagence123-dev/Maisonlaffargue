/**
 * 2015-2021 Bonpresta
 *
 * Bonpresta Column Category
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
 *  @author    Bonpresta
 *  @copyright 2015-2021 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*/

$(document).ready(function(){
    jQuery(document).ready(function(){
        jQuery('#boncategory ul.sf-menu').superfish({
            animation: {height:'show'},	// slide-down effect without fade-in
            delay:		 400			// 1.2 second delay on mouseout
        });
    });
    clickBonCategory();
    $( window ).resize(function() {
        clickBonCategory();
    });
});

function clickBonCategory() {
    $('#boncategory').on("click", function () {
        $('#boncategory .sf-menu').toggleClass("open");
    });
}

$('#boncategory .sf-menu').hover(
    function(){ $('#boncategory-overlay').addClass('hover') },
    function(){ $('#boncategory-overlay').removeClass('hover'); }
);