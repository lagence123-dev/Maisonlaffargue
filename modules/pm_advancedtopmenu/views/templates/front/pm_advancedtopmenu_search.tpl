<form method="get" action="{$atm_form_action_link|escape:'htmlall':'UTF-8'}" class="searchboxATM">
	<label for="{$atm_search_id|escape:'htmlall':'UTF-8'}">{if $atm_withExtra && $atm_have_icon}<img src="{$atm_icon_image_source|escape:'htmlall':'UTF-8'}" alt="" title="" class="adtm_menu_icon" />{/if}</label>
	<input type="hidden" name="controller" value="search" />
	<input type="hidden" name="orderby" value="position" />
	<input type="hidden" name="orderway" value="desc" />
	<input type="text" class="form-control search_query_atm" id="{$atm_search_id|escape:'htmlall':'UTF-8'}" name="search_query" value="{$atm_search_value|escape:'htmlall':'UTF-8'}" {if isset($atm_search_value) && !empty($atm_search_value)}onfocus="javascript:if(this.value=='{$atm_search_value|escape:'htmlall':'UTF-8'}')this.value='';" onblur="javascript:if(this.value=='')this.value='{$atm_search_value|escape:'htmlall':'UTF-8'}';"{/if} />
	<button type="submit" name="submit_search" class="adtm_search_submit_button">
		<i class="material-icons search"></i>
	</button>
</form>
