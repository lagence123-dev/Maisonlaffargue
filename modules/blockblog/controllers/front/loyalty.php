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

class BlockblogLoyaltyModuleFrontController extends ModuleFrontController
{
    public $php_self;


    public function init()
    {
        $module_name = 'blockblog';

        $loyality_onl = Configuration::get($module_name.'loyality_onl');

        if ($loyality_onl != 1){
            Tools::redirect('index.php');
        }
        parent::init();
    }

    public function setMedia()
    {
        parent::setMedia();

        $module_name = 'blockblog';


        if(Configuration::get($module_name.'d_eff_loyalty_myl') != "disable_all_effects") {

            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/js/wow.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/css/animate.css');

        }

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$module_name.'/views/css/font-custom.min.css');

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$module_name.'/views/css/admin.css');

    }


    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {

        $module_name = 'blockblog';
        $this->php_self = 'module-'.$module_name.'-loyalty';


        parent::initContent();

        $cookie = Context::getContext()->cookie;
        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;

        if (!$id_customer)
            Tools::redirect('authentication.php');



        include_once(_PS_MODULE_DIR_.$module_name.'/classes/loyalityblog.class.php');
        $obj = new loyalityblog();

        include_once(_PS_MODULE_DIR_.$module_name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();




        include_once(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'.php');
        $obj_module = new $module_name();
        $data_translate = $obj_module->translateCustom();

        $loyalty_prefix = $obj_module->getLoyalityPrefix();


        $obj_module->setSEOUrls();

        $obj_module->setControllersSettings();


        $obj_module->setLoyalitySettings();


        $gp = (int)Tools::getValue('gp');
        $step = (int)$obj->getStepMy();

        $start = (int)(($gp - 1)*$step);
        if($start<0)
            $start = 0;


        $data_my= $obj->getMyLoyaltyPoints(array('id_customer'=>$id_customer,'start'=>$start));

        $count_my = $data_my['count_all'];
        $used_points = $data_my['used_points'];
        $not_used_points = $data_my['not_used_points'];



        $data_url = $obj_blog->getSEOURLs();
        $loyalty_account_url = $data_url['loyalty_account_url'];
        $my_account = $data_url['my_account'];


        $paging = $obj->paging17(
            array('start'=>$start,
                'step'=> $step,
                'count' => $count_my,
                'action'=>'myloyalty',

            )
        );


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $data_translate['my_loyalty_meta_title'];
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $data_translate['my_loyalty_meta_description'];
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $data_translate['my_loyalty_meta_keywords'];
        }

        $this->context->smarty->assign('meta_title' , $data_translate['my_loyalty_meta_title']);
        $this->context->smarty->assign('meta_description' , $data_translate['my_loyalty_meta_description']);
        $this->context->smarty->assign('meta_keywords' , $data_translate['my_loyalty_meta_keywords']);


        $data_voucher = $obj->getVoucherValue();
        $voucher_points = $data_voucher['voucher_points'];
        $my_currency_iso_code = $data_voucher['my_currency_iso_code'];

        $rewrite_delimeter = '&';
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $rewrite_delimeter = '?';
        }


        $this->context->smarty->assign(array(
            $module_name.'d_eff_loyalty_my' => Configuration::get($module_name.'d_eff_loyalty_my'.$loyalty_prefix),

            $module_name.'my_data' => $data_my['data'],
            $module_name.'paging' => $paging,
            'transformation_allowed' => $not_used_points > 0,
            'loyalty_account_url'=>$loyalty_account_url,

            $module_name.'my_a_link'=> $my_account,

            'used_points'=>$used_points,
            'not_used_points'=>$not_used_points,

            'voucher'=>$voucher_points*$not_used_points,
            'is_16'=>version_compare(_PS_VERSION_, '1.7', '<'),
            'rewrite_delimeter'=>$rewrite_delimeter,
            'my_currency_iso_code'=>$my_currency_iso_code,


        ));



        if(version_compare(_PS_VERSION_, '1.7', '<')){
            $discounts1 = Discount::getCustomerDiscounts((int)($cookie->id_lang),(int)($cookie->id_customer), true, false);
        } else {
            $discounts1 = array();
            $discounts_all = CartRule::getCustomerCartRules(
                (int)($cookie->id_lang),(int)($cookie->id_customer),
                true,
                false
            );


            foreach ($discounts_all as $key => $voucher) {
                $discounts1[$key] = $voucher;
                $discounts1[$key]['voucher_date'] = Tools::displayDate($voucher['date_to'], null, false);
                $discounts1[$key]['voucher_minimal'] = ($voucher['minimum_amount'] > 0) ? Tools::displayPrice($voucher['minimum_amount'], (int)$voucher['minimum_amount_currency']) : $this->trans('None', array(), 'Shop.Theme');
                $discounts1[$key]['voucher_cumulable'] = $this->getCombinableVoucherTranslation($voucher);;

                $cartRuleValue = $this->accumulateCartRuleValue($voucher);

                if (0 === count($cartRuleValue)) {
                    $discounts1[$key]['value'] = '-';
                } else {
                    $discounts1[$key]['value'] = implode(' + ', $cartRuleValue);
                }
            }


        }

        //$discounts1 = array();
        $nbDiscounts = 0;
        $discounts = array();
        foreach ($discounts1 AS $discount) {
            // delete used coupons
            if($discount['quantity'] == 0)
                continue;

            $vouchercode = Configuration::get($module_name.'vouchercode'.$loyalty_prefix);
            if (stripos($discount['name'], $vouchercode) !== FALSE) {
                $discounts[] = $discount;

                if ($discount['quantity_for_user'])
                    $nbDiscounts++;

            }
        }

        if(count($discounts)>0)
            rsort($discounts);

        $this->context->smarty->assign(array('nbDiscounts' => (int)($nbDiscounts), 'discount' => $discounts));



        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:' . $module_name . '/views/templates/front/my-loaylty17.tpl');
        }else {
            $this->setTemplate('my-loaylty.tpl');
        }
    }


    public function postProcess()
    {
        if (Tools::getValue('process') == 'transformpoints')
            $this->processTransformPoints();
    }

    public function processTransformPoints()
    {
        $module_name = 'blockblog';

        $cookie = Context::getContext()->cookie;
        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;

        if (!$id_customer)
            Tools::redirect('authentication.php');

        $loyality_onl = Configuration::get($module_name.'loyality_onl');

        if ($loyality_onl != 1){
            Tools::redirect('index.php');
        }


        include_once(_PS_MODULE_DIR_.$module_name.'/classes/loyalityblog.class.php');
        $obj = new loyalityblog();
        $obj->createVoucher(array('id_customer'=>$id_customer));


        include_once(_PS_MODULE_DIR_.$module_name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $data_url = $obj_blog->getSEOURLs();
        $loyalty_account_url = $data_url['loyalty_account_url'];


        Tools::redirect($loyalty_account_url);
        exit;
    }



    protected function getCombinableVoucherTranslation($voucher)
    {
        if ($voucher['cart_rule_restriction']) {
            $combinableVoucherTranslation = $this->trans('No', array(), 'Shop.Theme');
        } else {
            $combinableVoucherTranslation = $this->trans('Yes', array(), 'Shop.Theme');
        }

        return $combinableVoucherTranslation;
    }

    /**
     * @param $hasTaxIncluded
     * @param $amount
     * @param $currencyId
     * @return string
     */
    protected function formatReductionAmount($hasTaxIncluded, $amount, $currencyId)
    {
        if ($hasTaxIncluded) {
            $taxTranslation = $this->trans('Tax included', array(), 'Shop.Theme.Checkout');
        } else {
            $taxTranslation = $this->trans('Tax excluded', array(), 'Shop.Theme.Checkout');
        };

        return sprintf(
            '%s ' . $taxTranslation,
            Tools::displayPrice($amount, (int) $currencyId)
        );
    }


    /**
     * @param $percentage
     * @return string
     */
    protected function formatReductionInPercentage($percentage)
    {
        return sprintf('%s%%', $percentage);
    }

    /**
     * @param $voucher
     * @return array
     */
    protected function accumulateCartRuleValue($voucher)
    {
        $cartRuleValue = array();

        if ($voucher['reduction_percent'] > 0) {
            $cartRuleValue[] = $this->formatReductionInPercentage($voucher['reduction_percent']);
        }

        if ($voucher['reduction_amount'] > 0) {
            $cartRuleValue[] = $this->formatReductionAmount(
                $voucher['reduction_tax'],
                $voucher['reduction_amount'],
                $voucher['reduction_currency']
            );
        }

        if ($voucher['free_shipping']) {
            $cartRuleValue[] = $this->trans('Free shipping', array(), 'Shop.Theme.Checkout');
        }

        if ($voucher['gift_product'] > 0) {
            $cartRuleValue[] = Product::getProductName(
                $voucher['gift_product'],
                $voucher['gift_product_attribute']
            );
        }

        return $cartRuleValue;
    }
}