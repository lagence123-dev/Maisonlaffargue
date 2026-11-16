<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
    /*
    * module: wkgiftcard
    * date: 2025-03-25 20:24:06
    * version: 5.2.1
    */
    protected function getTemplate($template_name)
    {
        if ($this->order_invoice && Module::isInstalled('wkgiftcard') && Module::isEnabled('wkgiftcard')) {
            include_once _PS_MODULE_DIR_ . 'wkgiftcard/classes/giftCardOrderDetails.php';
            if ($orderDetails = $this->order_invoice->getProducts()) {
                $objGiftCardOrderDetails = new GiftCardOrderDetails();
                $GiftCardProductExists = 1;
                foreach ($orderDetails as $id => &$oD) {
                    if ($oD['reduction_amount_tax_excl'] > 0) {
                        $uPTEBSP = $oD['unit_price_tax_excl_including_ecotax'] + $oD['reduction_amount_tax_excl'];
                        $oD['unit_price_tax_excl_before_specific_price'] = $uPTEBSP;
                    } elseif ($oD['reduction_percent'] > 0) {
                        if ($oD['reduction_percent'] == 100) {
                            $oD['unit_price_tax_excl_before_specific_price'] = 0;
                        } else {
                            $rP = (100 - $oD['reduction_percent']);
                            $uPTEBSP = (100 * $oD['unit_price_tax_excl_including_ecotax']) / $rP;
                            $oD['unit_price_tax_excl_before_specific_price'] = $uPTEBSP;
                        }
                    }
                    $taxes = OrderDetail::getTaxListStatic($id);
                    $taxTemp = [];
                    foreach ($taxes as $tax) {
                        $obj = new Tax($tax['id_tax']);
                        $translator = Context::getContext()->getTranslator();
                        $taxTemp[] = $translator->trans(
                            '%taxrate%%space%%',
                            [
                                '%taxrate%' => ($obj->rate + 0),
                                '%space%' => '&nbsp;',
                            ],
                            'Shop.Pdf'
                        );
                    }
                    $oD['order_detail_tax'] = $taxes;
                    $oD['order_detail_tax_label'] = implode(', ', $taxTemp);
                    if ($giftCardProductOrderInfo = $objGiftCardOrderDetails->getGiftCardOrderInfoByProductID(
                        $oD['product_id'],
                        Context::getContext()->shop->id
                    )) {
                        if ($giftCardProductOrderInfo && is_array($giftCardProductOrderInfo)) {
                            $GiftCardProductExists = 1;
                        }
                    }
                }
                unset($taxTemp);
                unset($oD);
                if (Configuration::get('PS_PDF_IMG_INVOICE')) {
                    foreach ($orderDetails as &$oD) {
                        if ($oD['image'] != null) {
                            $name = 'product_mini_'
                            . (int) $oD['product_id']
                            . (isset($oD['product_attribute_id']) ? '_'
                            . (int) $oD['product_attribute_id'] : '')
                            . '.jpg';
                            $path = _PS_PROD_IMG_DIR_ . $oD['image']->getExistingImgPath() . '.jpg';
                            $oD['image_tag'] = preg_replace(
                                '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                                _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                                ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                                1
                            );
                            if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                                $oD['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                            } else {
                                $oD['image_size'] = false;
                            }
                        }
                    }
                    unset($oD); // don't overwrite the last order_detail later
                }
                if ($GiftCardProductExists) {
                    foreach ($orderDetails as &$oD) {
                        if ($giftCardProductOrderInfo = $objGiftCardOrderDetails->getGiftCardOrderInfoByProductID(
                            $oD['product_id'],
                            Context::getContext()->shop->id
                        )) {
                            $oD['customizedDatas'] = [];
                        }
                    }
                    $this->smarty->assign('order_details', $orderDetails);
                }
            }
        }
        return parent::getTemplate($template_name);
    }
}
