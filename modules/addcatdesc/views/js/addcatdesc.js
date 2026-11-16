/**
* addcatdesc module javascript file.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*/

$(document).ready(function(){
    if (showFp == 1) {
            prestashop.on('updateProductList', function (event) {
                if ((event.current_url.indexOf('page=') != -1) || (event.current_url.indexOf('q=') != -1)) {
                    if ($('#addcatdesc_wrapper').length > 0) {
                        $('#addcatdesc_wrapper').text('');    
                    }
                } else if (event.current_url.indexOf('page=') == -1) {
					$.ajax({
						type: 'GET',
						url: ajax_addcatdesc_link,
						async: true,
						cache: false,
						dataType : "html",
						success: function(result)
						{
							$('#addcatdesc_wrapper').html(result);
						},
						error: function(XMLHttpRequest, textStatus, errorThrown){
							//alert("Error in data");
						}
        		});
				}
            });
    }
    if (showFpFaq == 1) {
        prestashop.on('updateProductList', function (event) {
                if ((event.current_url.indexOf('page=') != -1) || (event.current_url.indexOf('q=') != -1)) {
                    if ($('#addcatdesc_wrapper_faq').length > 0) {
                        $('#addcatdesc_wrapper_faq').text('');    
                    }
                } else if (event.current_url.indexOf('page=') == -1) {
					$.ajax({
						type: 'GET',
						url: ajax_addcatdesc_faq_link,
						async: true,
						cache: false,
						dataType : "html",
						success: function(result)
						{
							$('#addcatdesc_wrapper_faq').html(result);
						},
						error: function(XMLHttpRequest, textStatus, errorThrown){
							//alert("Error in data");
						}
        		});
				}
        });
    }
});

function showAddCatDesc(el){
	if ($(".addcatdesc_wrapper").hasClass('full_height')) {
        $(".addcatdesc_wrapper").removeClass('full_height');
		$(".wide-button").text(moreTxt);
    } else {
        $(".addcatdesc_wrapper").addClass('full_height');
        $(".wide-button").text(lessTxt);
    }
}