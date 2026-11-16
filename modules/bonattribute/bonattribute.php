<?php
/**
 * 2015-2021 Bonpresta
 *
 * Bonpresta Product Attributes and Combinations
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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Bonattribute extends Module
{
    protected $templatePath;
    public function __construct()
    {
        $this->name = 'bonattribute';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->bootstrap = true;
        $this->author = 'Bonpresta';
        $this->module_key = '501dfbfe7bed75b0385446410aff54db';
        parent::__construct();
        $this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
        $this->id_shop = Context::getContext()->shop->id;
        $this->displayName = $this->l('Product Attributes and Combinations');
        $this->description = $this->l('Display attribute and combinations in product list.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->templatePath = 'module:'.$this->name.'/views/templates/hook/';

    }

    protected function getConfigurations()
    {
        $configurations = array(
            'BON_ATTRIBUTE_ENABLE' => true,
            'BON_TYPE_ATTR' => 'radio_buttons'
        );

        return $configurations;
    }

    public function install()
    {
        $configurations = $this->getConfigurations();

        foreach ($configurations as $name => $config) {
            Configuration::updateValue($name, $config);
        }
        return parent::install() &&
        $this->registerHook('displayBonAttribute') &&
        $this->registerHook('displayAttributeButton') &&
        $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        $configurations = $this->getConfigurations();

        foreach (array_keys($configurations) as $config) {
            Configuration::deleteByName($config);
        }

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';
        $result = '';

        if ((bool)Tools::isSubmit('submitSettings')) {
            if (!$result = $this->preValidateForm()) {
                $output .= $this->postProcess();
                $output .= $this->displayConfirmation($this->l('Save all settings.'));
            } else {
                $output = $result;
                $output .= $this->renderTabForm();
            }
        }

        if (!$result) {
            $output .= $this->renderTabForm();
        }

        return $output;
    }

    protected function renderTabForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable:'),
                        'name' => 'BON_ATTRIBUTE_ENABLE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'enable',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'disable',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'form_group_class' => 'bon_modules_attr_type',
                        'label' => $this->l('Attribute type'),
                        'name' => 'BON_TYPE_ATTR',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'drop_down_list',
                                    'name' => $this->l('Drop-down list')
                                ),
                                array(
                                    'id' => 'radio_buttons',
                                    'name' => $this->l('Radio buttons')
                                ),
                                
                            ),
                            'name' => 'name',
                            'id' => 'id',
                            'index' => 'BON_TYPE_ATTR'
                        ),
                        'col' => 2,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSettings';
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        $fields = array();
        $configurations = $this->getConfigurations();

        foreach (array_keys($configurations) as $config) {
            $fields[$config] = Configuration::get($config);
        }

        return $fields;
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFieldsValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    protected function preValidateForm()
    {
        $errors = array();

        if (count($errors)) {
            return $this->displayError(implode('<br />', $errors));
        }

        return false;
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get('BON_ATTRIBUTE_ENABLE')) {
            $this->context->controller->addJS($this->_path . '/views/js/bonattribute.js');
            $this->context->controller->addCSS($this->_path . '/views/css/bonattribute.css');
            Media::addJsDefL('bon_attribute_url', $this->_path.'controllers/front/ajax.php');
            Media::addJsDefL('static_token_bon_attribute', Tools::getToken(false));
            Media::addJsDefL('static_token_bon_attribute', Tools::getToken(false));
            $https = '';
            if (Configuration::get('PS_SSL_ENABLED')) {
                $https = 'https://';
            } else {
                $https = 'http://';
            }
            Media::addJsDefL('https', $https);
            Media::addJsDefL('bon_type_attr', Configuration::get('BON_TYPE_ATTR'));

        }
    }

	public function hookDisplayProductPriceBlock($params)
	{
			    $productObj = new Product($params['product']['id_product']);
			    $product = $this->productPrepare($productObj, $params['product']['id_product_attribute'], $params['product']['quantity']);
                $mainArr = array();
                $result = array();
				$attributeCombinations = array();
				$productData = array();
                $productPrice = array();
                $attributeGroupsSelect = array();
				$attributeCombinations = $productObj->getAttributeCombinations((int)$this->context->language->id);
				$productData = $this->attributeCombinationForSelect($attributeCombinations, $productObj, $product);
                $attributes_groups = $productObj->getAttributesGroups($this->context->language->id);
                
				foreach ($attributeCombinations as $item){
					$attributeGroupsSelect[$item['id_product_attribute']][] = array(
						'id_attribute_group' => $item['id_attribute_group'],
						'id_attribute' => $item['id_attribute'],
					);
				}
                
                foreach ($attributes_groups as $key => $row) {      
                    $mainArr[$key]['id_product_attribute'] = $row['id_product_attribute'];     
                    $mainArr[$key]['id_attribute_group'] = $row['id_attribute_group'];     
                    $mainArr[$key]['attribute_name'] = $row['attribute_name']; 
                    $mainArr[$key]['group_name'] = $row['group_name']; 
                    $mainArr[$key]['public_group_name'] = $row['public_group_name']; 
                    $mainArr[$key]['group_type'] = $row['group_type']; 
                    $mainArr[$key]['attribute_color'] = $row['attribute_color']; 
                    $mainArr[$key]['texture'] =  (@filemtime(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) ? _THEME_COL_DIR_ . $row['id_attribute'] . '.jpg' : '';   
                    $mainArr[$key]['quantity'] = $row['quantity'];
                }

                array_walk($mainArr, function(&$productPrice) use ($productData) {
                    $product_id = $productPrice['id_product_attribute'];
                    if (isset($productData[$product_id]['prices'])) {
                        $productPrice['prices'] = $productData[$product_id]['prices'];
                    }
                  
                });

			    $limitLats = [];
                $actuallyFiltered = [];
                
                foreach($mainArr as $cluster) {

                    if (true === array_key_exists($cluster['attribute_name'], $actuallyFiltered)) {
                    
                        $limitLats[$cluster['id_product_attribute']] = $cluster['attribute_name'];
                        continue;
                    }
                
                    $actuallyFiltered[$cluster['attribute_name']] = $cluster;
                
                }
          
                // var_export($limitLats);

                foreach ($mainArr as $item) {
                    $id = $item['id_product_attribute']; 
                    if (!isset($result[$id])) {
                      $result[$id] = $item;
                    } else {
                      $result[$id]['attribute_name'] .= ', ' . $item['attribute_name'];
                    // $result[$id]['attribute_color'] = $item['attribute_color'];
                  }
                }
                
                $public_group_name = '';

                if (isset($mainArr[0])) {
                    $public_group_name = $mainArr[0]['public_group_name'];
                }

                $this->context->smarty->assign(array(
                    'public_group_name' => $public_group_name,
                    'bon_data_id_product' => $params['product']['id_product'],
                    'bon_attr_list_groups' => $actuallyFiltered,
                    'result' => $result,
                    'productData' => $productData,
                    'type_attr' => Configuration::get('BON_TYPE_ATTR') 
                 ));
						
            return $this->fetch($this->templatePath.'bonattribute.tpl');
	
	}


	public function productPrepare($productObj, $id_product_attribute = false, 	$quantity = '')
	{
		$productObj->loadStockData();
		$product = (array)$productObj;
		$product['id_product_attribute'] = (int) Tools::getValue('id_product_attribute');
       
		if ($id_product_attribute | (!$product['id_product_attribute'] && $id_product_attribute)){
			$product['id_product_attribute'] = $id_product_attribute;
		}

		$product['images'] = $productObj->getImages($this->context->language->id);
		$product['id_product'] = (int) $productObj->id;
		$product['link_rewrite'] = $this->checkLinkRewrite($productObj->link_rewrite);

		return $product;
	}

    public function checkLinkRewrite($link_rewrite)
    {
        $link_rewrite = $link_rewrite;

        if (is_array($link_rewrite)) {
            $filteredArray = array_filter($link_rewrite);
            $link_rewrite = current($filteredArray);
        }

        return $link_rewrite;
    }

	public function attributeCombinationForSelect($attributeCombinations, $productObj)
	{
		$productsAttr = array();

		foreach ($attributeCombinations as $attributeCombination) {
			$product = $this->productPrepare($productObj, $attributeCombination['id_product_attribute']);
			$prices = $this->getPrices($productObj, $product);
			$productsAttr[$attributeCombination['id_product_attribute']]['prices'] = $prices;
		}

		return $productsAttr;
	}

	public function getPrices($productObj, $product)
    {
		$prices = array();
		$taxConfiguration = new TaxConfiguration();		
        $price = $productObj->getPrice(true, $product['id_product_attribute']);
		$productProperties = Product::getProductProperties($this->context->language->id, $product, $this->context);

	    if ($productProperties['specific_prices']) {
 	        // $prices['has_discount'] = (0 != $productProperties['reduction']);
	        // $prices['discount_type'] = $productProperties['specific_prices']['reduction_type'];
	        $prices['discount_percentage'] = -round(100 * $productProperties['specific_prices']['reduction']).'%';
	        $prices['discount_percentage_absolute'] = round(100 * $productProperties['specific_prices']['reduction']).'%';
	        $prices['discount_amount'] = Tools::displayPrice($productProperties['reduction']);
//	        $prices['regular_price'] = Tools::displayPrice($productProperties['price_without_reduction']);
	    }
		
		$prices['price'] = Tools::displayPrice($price); 

		return $prices;
	}

    public function hookDisplayAttributeButton($params)
    {
       
        $productObj = new Product($params['product']['id_product'], false);
        $attributes_groups = $productObj->getAttributesGroups($this->context->language->id);
       
        $mainArr = array();
        if (is_array($attributes_groups) && $attributes_groups) {     
             
            return $this->fetch($this->templatePath.'bonattribute-button.tpl');
        }
        
        return false;
    }

    public function hookdisplayBonAttribute($params)
    {
        return $this->hookDisplayProductPriceBlock($params);
        
    }
}
