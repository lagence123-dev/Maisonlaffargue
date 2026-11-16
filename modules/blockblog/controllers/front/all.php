<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
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
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class BlockblogAllModuleFrontController extends ModuleFrontController
{
    private $_name_module = "blockblog";
	public function init()
	{
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
		}
		
		$http_referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
			
		
		include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
		$obj = new blogspm();
		$is_friendly_url = $obj->isURLRewriting();
		
		if($is_friendly_url){
			
			
		if(Tools::strlen($http_referrer)>0){
				
			$lang_iso_redirect = Language::getIsoById((int)(Tools::getValue("id_lang")));
		
			$languages = Language::getLanguages(false);
			foreach ($languages as $language){
				$iso_array = Language::getIsoById((int)($language['id_lang']));
				if(preg_match('/\/'.$iso_array.'\//i',$http_referrer)){
					$iso_code = $iso_array;
					break;
				}
			}
			$seo_friendly_url = '';
			$item_seo_url = '';
			#### for change language ###
			$post_seo_url = Tools::getValue('post_id');
			if($post_seo_url){
			$id_post = $obj->getIdPostifFriendlyURLEnable(array('seo_url'=>$post_seo_url,
																'id_lang'=>Language::getIdByIso($iso_code))
														  );
			$seo_friendly_url = $obj->getSEOFriendlyURLifFriendlyURLEnable(array('id_post'=>$id_post,
																				 'id_lang'=>Language::getIdByIso($lang_iso_redirect)
																				 )
																	   );
			//var_dump($seo_friendly_url); var_dump($id_post); var_dump($post_seo_url); exit;
			if(empty($seo_friendly_url)) $seo_friendly_url = $post_seo_url;
			$item_seo_url = $post_seo_url;
			}
			#### for change language ###
			
																		   
			#### for change language ###
			$cat_seo_url = Tools::getValue('category_id');
			if($cat_seo_url){
			$id_cat = $obj->getIdCatifFriendlyURLEnable(array('seo_url'=>$cat_seo_url,
																'id_lang'=>Language::getIdByIso($iso_code))
														  );
			$seo_friendly_url = $obj->getSEOFriendlyURLifFriendlyURLEnableCat(array('id_cat'=>$id_cat,
																				 'id_lang'=>Language::getIdByIso($lang_iso_redirect)
																				 )
																   );
			if(empty($seo_friendly_url)) $seo_friendly_url = $cat_seo_url;
			$item_seo_url = $cat_seo_url;
			
			}
			#### for change language ###
			
				$to = '/'.$iso_code.'/';
				$from = '/'.$lang_iso_redirect.'/';
				$http_referrer = str_replace($to,$from,$http_referrer);
				
				$to = $item_seo_url;
				$from = $seo_friendly_url;
				$http_referrer = str_replace($to,$from,$http_referrer);
				
			
		} else {
			$request_uri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
			$query_string = isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'';
			
			
			//http://localhost:97/it/module/blockblog/all?y=2014&m=1 - archives
			//http://localhost:97/en/blog?y=2014&m=1
			$y_seo_url = Tools::getValue('y');
			$m_seo_url = Tools::getValue('m');
			if($y_seo_url && $m_seo_url){
				$explode_request_url = explode("/",$request_uri);
				$lang_iso = isset($explode_request_url[1])?"/".$explode_request_url[1]:"";
				$http_referrer = $_http_host.$lang_iso."/blog?".$query_string;
			}
				
			//http://host.com/en/blog?search=post
			$search_seo_url = Tools::getValue('search');
			if($search_seo_url){
				$explode_request_url = explode("/",$request_uri);
				$lang_iso = isset($explode_request_url[1])?"/".$explode_request_url[1]:"";
				$http_referrer = $_http_host.$lang_iso."/blog?".$query_string;
			}
			
			//http://host.com/es/module/blockblog/all - categories
			//http://host.com/en/blog/categories
			if(Tools::strlen($query_string)==0){
				$explode_request_url = explode("/",$request_uri);
				$lang_iso = isset($explode_request_url[1])?"/".$explode_request_url[1]:"";
				$http_referrer = $_http_host.$lang_iso."/blog/categories";
			}
				
			//http://host.com/fr/module/blockblog/all?post_id=pravila-uhoda-za-ukrasheniyami-iz-dereva - post	
			//http://host.com/en/blog/post/pravila-uhoda-za-ukrasheniyami-iz-dereva
			$post_seo_url = Tools::getValue('post_id');
			if($post_seo_url){
				$explode_request_url = explode("/",$request_uri);
				$lang_iso = isset($explode_request_url[1])?"/".$explode_request_url[1]:"";
				$http_referrer = $_http_host.$lang_iso."/blog/post/".$post_seo_url;
			}
			
			//http://host.com/es/module/blockblog/all?category_id=category-en - category
			//http://host.com/fr/blog/category/category-en
			$cat_seo_url = Tools::getValue('category_id');
			if($cat_seo_url){
				$explode_request_url = explode("/",$request_uri);
				$lang_iso = isset($explode_request_url[1])?"/".$explode_request_url[1]:"";
				$http_referrer = $_http_host.$lang_iso."/blog/category/".$cat_seo_url;
			}
			
			if(Tools::strlen($http_referrer)==0){
				$explode_request_url = explode("/",$request_uri);
				$lang_iso = isset($explode_request_url[1])?$explode_request_url[1]:"";
				$http_referrer = $_http_host.$lang_iso."/blog";
			}
				
		}
		
		}
		Tools::redirect($http_referrer);
		parent::init();
	}
	
	public function setMedia()
	{
		parent::setMedia();
	}

	
	public function initContent()
	{
		
		parent::initContent();


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:blockblog/views/templates/front/all17.tpl');
        }else {
            $this->setTemplate('all.tpl');
        }

	}
}