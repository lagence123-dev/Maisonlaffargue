<?php
/**
 * 2015-2021 Bonpresta
 *
 * Bonpresta Product Compare
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


require_once('../../../../config/config.inc.php');
require_once('../../../../init.php');
require_once(_PS_MODULE_DIR_.'/bonattribute/bonattribute.php');
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

$bonattribute = new Bonattribute();

$get_data = Tools::file_get_contents('php://input');
$data = json_decode($get_data);
$id_shop = Context::getContext()->shop->id;
$product = new Product(Tools::getValue('bon_id'), true, (int)$context->language->id);

$BonIdProduct = $data->bonIdProduct;

$BonIdAttr = $data->dataIdAttr;
$images = new Image($BonIdProduct, (int)$context->language->id);
$link = new Link();

$imgIdArr = $product->getCombinationImageById($BonIdAttr, (int)$context->language->id);

$imgId = $imgIdArr['id_image'];


if (isset($imgId)) {
    die(json_encode(array('img' => $link->getImageLink($imgId, $imgId, 'home_default'))));
}




