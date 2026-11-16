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
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;

class WkGiftCardObj extends ObjectModel
{
    public $together;
    public $free_shipping;
    public $dynamic_price;
    public $amount;
    public $active;
    public $choose_product;
    public $id_voucher_product;
    public $point_type;
    public $valid_upto;
    public $is_category_restriction;
    public $allow_categories;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_gift_card',
        'primary' => 'id',
        'fields' => [
            'id_voucher_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'together' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'free_shipping' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'dynamic_price' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'active' => ['type' => self::TYPE_INT, 'shop' => true],
            'choose_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'valid_upto' => ['type' => self::TYPE_INT, 'shop' => true],
            'amount' => ['type' => self::TYPE_FLOAT,  'required' => true, 'shop' => true],
            'point_type' => ['type' => self::TYPE_STRING, 'required' => true, 'shop' => true],
            'is_category_restriction' => ['type' => self::TYPE_INT, 'shop' => true],
            'allow_categories' => ['type' => self::TYPE_STRING, 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE,  'validate' => 'isDate', 'shop' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        Shop::addTableAssociation('wk_gift_card', ['type' => 'shop', 'primary' => 'id']);
        parent::__construct($id, $idLang, $idShop);
    }

    public function getProductDetailById($idVoucherProduct, $idShop)
    {
        return Db::getInstance()->getRow(
            'SELECT `name`,`link_rewrite`,`id_product` FROM `' . _DB_PREFIX_ . 'product_lang`
             WHERE `id_shop` = ' . (int) $idShop . ' AND `id_product` = ' . (int) $idVoucherProduct
        );
    }

    public function checkVoucherIdByProductId($productId)
    {
        return Db::getInstance()->getValue(
            'SELECT `id` FROM `' . _DB_PREFIX_ . 'wk_gift_card`
            WHERE `id_voucher_product` = ' . (int) $productId
        );
    }

    public function getActiveVoucherIdByProductId($productId)
    {
        return Db::getInstance()->getValue(
            'SELECT `id` FROM `' . _DB_PREFIX_ . 'wk_gift_card`
            WHERE `id_voucher_product` = ' . (int) $productId . ' AND `active` = 1'
        );
    }

    public static function isDynamicPriceProduct($productId, $idShop)
    {
        $sql = 'SELECT wgcs.`dynamic_price` FROM `' . _DB_PREFIX_ . 'wk_gift_card` wgc
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_gift_card_shop` wgcs ON (wgcs.`id` = wgc.`id` AND wgcs.`id_shop` = ' . (int) $idShop . ')
            WHERE wgcs.`id_shop` = ' . (int) $idShop . ' AND wgcs.`id_voucher_product` = ' . (int) $productId;

        return Db::getInstance()->getValue(
            $sql
        );
    }

    public function getProductAllInfo($idVoucher, $idShop)
    {
        $sql = 'SELECT wgcs.* FROM `' . _DB_PREFIX_ . 'wk_gift_card` wgc
        LEFT JOIN `' . _DB_PREFIX_ . 'wk_gift_card_shop` wgcs ON (wgcs.`id` = wgc.`id` AND wgcs.`id_shop` = ' . (int) $idShop . ')
        WHERE wgcs.`id_shop` = ' . (int) $idShop . ' AND wgcs.`id_voucher_product` = ' . (int) $idVoucher;

        return Db::getInstance()->getRow(
            $sql
        );
    }

    public function getProductAmountInfo($idVoucher, $idShop)
    {
        $sql = 'SELECT wgcs.`point_type`, wgcs.`amount` FROM `' . _DB_PREFIX_ . 'wk_gift_card` wgc
        LEFT JOIN `' . _DB_PREFIX_ . 'wk_gift_card_shop` wgcs ON (wgcs.`id` = wgc.`id` AND wgcs.`id_shop` = ' . (int) $idShop . ')
        WHERE wgcs.`id_shop` = ' . (int) $idShop . ' AND wgcs.`id_voucher_product` = ' . (int) $idVoucher;

        return Db::getInstance()->getRow(
            $sql
        );
    }

    public function getVoucherProductDetail($productId, $idShop)
    {
        $sql = 'SELECT pl.`name`
                FROM `' . _DB_PREFIX_ . 'product` p
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_shop` = ' . (int) $idShop . ')
                WHERE pl.`id_shop` = ' . (int) $idShop . ' AND pl.`id_product` =' . (int) $productId;
        $gift_product_details = Db::getInstance()->getValue($sql);

        if ($gift_product_details) {
            return $gift_product_details;
        } else {
            return false;
        }
    }

    public function productSelection($currentId, $giftCardId, $qty, $idShop)
    {
        Db::getInstance()->insert('cart_rule_product_rule_group', [
            'id_cart_rule' => (int) $currentId,
            'quantity' => $qty,
        ]);
        $idProductRuleGroup = Db::getInstance()->Insert_ID();

        if ($idProductRuleGroup) {
            $getProductInfoDiffId = WkGiftCardProduct::getAllByGiftCardID($giftCardId);
            if ($getProductInfoDiffId) {
                Db::getInstance()->insert('cart_rule_product_rule', [
                    'id_product_rule_group' => (int) $idProductRuleGroup,
                    'type' => 'products',
                ]);

                $idProductRuleForProd = Db::getInstance()->Insert_ID();

                foreach ($getProductInfoDiffId as $value) {
                    Db::getInstance()->insert('cart_rule_product_rule_value', [
                        'id_product_rule' => (int) $idProductRuleForProd,
                        'id_item' => (int) $value['id_product'],
                    ]);
                }
            }

            $allowCategories = WkGiftCardProduct::getAllowedCategory($giftCardId);
            if (isset($allowCategories['allow_categories']) && $allowCategories['allow_categories']) {
                Db::getInstance()->insert('cart_rule_product_rule', [
                    'id_product_rule_group' => (int) $idProductRuleGroup,
                    'type' => 'categories',
                ]);

                $idProductRuleForCat = Db::getInstance()->Insert_ID();
                $allowCategories = explode(',', $allowCategories['allow_categories']);
                foreach ($allowCategories as $value) {
                    Db::getInstance()->insert('cart_rule_product_rule_value', [
                        'id_product_rule' => (int) $idProductRuleForCat,
                        'id_item' => (int) $value,
                    ]);
                }
            }
        }
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            self::saveCartRuleToShop($currentId, $idShop);
        }
    }

    public static function updatePsProductImage($idProduct, $source)
    {
        Image::deleteCover((int) $idProduct);
        $objImage = new Image();
        $objImage->id_product = $idProduct;
        $objImage->position = Image::getHighestPosition($idProduct) + 1;

        $objImage->cover = 1;

        if ($objImage->add()) {
            $imageId = $objImage->id;

            $newPath = $objImage->getPathForCreation();
            $imagesTypes = ImageType::getImagesTypes('products');

            if ($imagesTypes) {
                foreach ($imagesTypes as $imageType) {
                    ImageManager::resize(
                        $source,
                        $newPath . '-' . Tools::stripslashes($imageType['name']) . '.' . $objImage->image_format,
                        $imageType['width'],
                        $imageType['height'],
                        $objImage->image_format
                    );
                }
            }

            ImageManager::resize($source, $newPath . '.' . $objImage->image_format);

            return $imageId;
        }

        return false;
    }

    public static function saveCartRuleToShop($idCartRule, $idShop)
    {
        return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_shop` (`id_cart_rule`, `id_shop`)
        values (' . (int) $idCartRule . ',' . (int) $idShop . ')');
    }

    /**
     * Load Prestashop category with ajax load of plugin jstree.
     */
    public static function getWkGiftCardProductCategory()
    {
        if (!Tools::getValue('id')) {
            // Add product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = [Category::getRootCategory()->id]; // Root Category will be automatically selected
        } else {
            // Edit product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = explode(',', Tools::getValue('catIds'));
        }
        $objGiftCard = new WkGiftCardObj();
        $treeLoad = $objGiftCard->getProductCategory(
            $catId,
            $selectedCatIds,
            Context::getContext()->language->id
        );
        if ($treeLoad) {
            exit(json_encode($treeLoad)); // ajax close
        } else {
            exit('fail'); // ajax close
        }
    }

    /**
     * Get prestashop jstree category
     *
     * @param int $catId node id of jstree category
     * @param int $selectedCatIds selected category in jstree
     * @param int $idLang content display of selected language
     *
     * @return category load
     */
    public function getProductCategory($catId, $selectedCatIds, $idLang)
    {
        if ($catId == '#') {
            // First time load
            $root = Category::getRootCategory();
            $category = Category::getHomeCategories($idLang, true);
            $categoryArray = [];
            foreach ($category as $catkey => $cat) {
                $categoryArray[$catkey]['id'] = $cat['id_category'];
                $categoryArray[$catkey]['text'] = $cat['name'];
                $subcategory = $this->getPsCategories($cat['id_category'], $idLang);
                $subChildSelect = false;
                if ($subcategory) {
                    $categoryArray[$catkey]['children'] = true;

                    foreach ($subcategory as $subcat) {
                        if (in_array($subcat['id_category'], $selectedCatIds)) {
                            $subChildSelect = true;
                        }
                    }
                } else {
                    $categoryArray[$catkey]['children'] = false;
                }

                if (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $categoryArray[$catkey]['state'] = ['opened' => true, 'selected' => true];
                } elseif (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == false) {
                    $categoryArray[$catkey]['state'] = ['selected' => true];
                } elseif (!in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $categoryArray[$catkey]['state'] = ['opened' => true];
                }
            }

            $treeLoad = [];
            if (in_array($root->id_category, $selectedCatIds)) {
                $treeLoad = ['id' => $root->id_category,
                    'text' => $root->name,
                    'children' => $categoryArray,
                    'state' => ['opened' => true, 'selected' => true],
                ];
            } else {
                $treeLoad = ['id' => $root->id_category,
                    'text' => $root->name,
                    'children' => $categoryArray,
                    'state' => ['opened' => true],
                ];
            }
        } else {
            // If sub-category is selected then its automatically called
            $childcategory = $this->getPsCategories($catId, $idLang);
            $treeLoad = [];
            $singletreeLoad = [];
            foreach ($childcategory as $cat) {
                $subcategoryArray = [];
                $subcategoryArray['id'] = $cat['id_category'];
                $subcategoryArray['text'] = $cat['name'];
                $subcategory = $this->getPsCategories($cat['id_category'], $idLang);

                $subChildSelect = false;
                if ($subcategory) {
                    $subcategoryArray['children'] = true;

                    foreach ($subcategory as $subcat) {
                        if (in_array($subcat['id_category'], $selectedCatIds)) {
                            $subChildSelect = true;
                        }
                    }
                } else {
                    $subcategoryArray['children'] = false;
                }

                if (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $subcategoryArray['state'] = ['opened' => true, 'selected' => true];
                } elseif (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == false) {
                    $subcategoryArray['state'] = ['selected' => true];
                } elseif (!in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $subcategoryArray['state'] = ['opened' => true];
                }

                $singletreeLoad[] = $subcategoryArray;
            }

            $treeLoad = $singletreeLoad;
        }

        return $treeLoad;
    }

    public function getPsCategories($idParent, $idLang)
    {
        return Db::getInstance()->executeS(
            'SELECT a.`id_category`, a.`id_parent`, l.`name`
            FROM `' . _DB_PREFIX_ . 'category` a
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` l ON (a.`id_category` = l.`id_category`)
            WHERE a.`id_parent` = ' . (int) $idParent . '
            AND l.`id_lang` = ' . (int) $idLang . '
            AND l.`id_shop` = ' . (int) Context::getContext()->shop->id . '
            AND a.`active` = 1
            ORDER BY a.`id_category`'
        );
    }

    public static function getGiftCardProducts($q = false, $type = false, $idLang = false, $orderby = false, $orderway = false, $start_point = 0, $limit_point = 10000000)
    {
        if (!$idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        if (!$orderway) {
            $orderway = 'desc';
        }

        $context = Context::getContext();
        $sql = 'SELECT wgcs.`id_voucher_product` as `id_product`
        FROM `' . _DB_PREFIX_ . 'wk_gift_card` wgc
        INNER JOIN `' . _DB_PREFIX_ . 'wk_gift_card_shop` wgcs ON (wgcs.`id` = wgc.`id`
        AND wgcs.`id_shop` = ' . (int) $context->shop->id . ')
        INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = wgc.`id_voucher_product`)
        INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_shop` = ' . (int) $context->shop->id . ' AND pl.`id_lang` = ' . (int) $context->language->id . ')
        INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (p.`id_product` = ps.`id_product`
        AND ps.`id_shop` = ' . (int) $context->shop->id . ' AND ps.`active` = TRUE)
        WHERE wgcs.`active` = 1';

        if ($type == 'd') {
            $sql .= ' AND wgcs.`dynamic_price` = 1';
        } else {
            if (Validate::isFloat($type) && $type != 0) {
                $sql .= ' AND wgcs.`amount` = ' . (float) $type;
            }
        }

        if (!empty($q)) {
            $sql .= ' AND pl.`name` LIKE \'%' . pSQL($q) . '%\'';
        }

        if (!$orderby) {
            $sql .= ' ORDER BY p.`id_product` ' . pSQL($orderway);
        } else {
            $sql .= ' ORDER BY pl.`' . $orderby . '` ' . pSQL($orderway);
        }
        $sql .= ' LIMIT ' . $start_point . ', ' . $limit_point;

        $giftCardProducts = Db::getInstance()->executeS($sql);

        if (!empty($giftCardProducts)) {
            return $giftCardProducts;
        }

        return false;
    }

    public static function getProductsPresenter($products = false)
    {
        $assembler = new ProductAssembler(Context::getContext());

        $presenterFactory = new ProductPresenterFactory(Context::getContext());
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                Context::getContext()->link
            ),
            Context::getContext()->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            Context::getContext()->getTranslator()
        );
        $productsForTemplate = [];
        if (is_array($products)) {
            foreach ($products as $rawProduct) {
                $productsForTemplate[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    Context::getContext()->language
                );
            }
        }

        return $productsForTemplate;
    }

    public static function addSqlAssociationCustom(
        $table,
        $alias,
        $inner_join = true,
        $on = null,
        $force_not_default = false,
        $identifier = 'id'
    ) {
        $table_alias = $table . '_shop';
        if (strpos($table, '.') !== false) {
            list($table_alias, $table) = explode('.', $table);
        }

        $asso_table = Shop::getAssoTable($table);
        if ($asso_table === false || $asso_table['type'] != 'shop') {
            return;
        }
        $sql = (($inner_join) ? ' INNER' : ' LEFT') . ' JOIN ' . _DB_PREFIX_ . $table . '_shop ' . $table_alias . '
        ON (' . $table_alias . '.' . $identifier . ' = ' . $alias . '.' . $identifier;
        if ((int) Shop::getContextShopID()) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . (int) Shop::getContextShopID();
        } elseif (Shop::checkIdShopDefault($table) && !$force_not_default) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . $alias . '.id_shop_default';
        } else {
            $sql .= ' AND ' . $table_alias . '.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')';
        }
        $sql .= (($on) ? ' AND ' . $on : '') . ')';

        return $sql;
    }

    /**
     * Get gift card product prices
     *
     * @param int idShop
     *
     * @return prices
     */
    public static function getStaticGiftCardPrice($idShop = false)
    {
        if ($idShop) {
            $idShop = Context::getContext()->shop->id;
        }
        $sql = 'SELECT wgcs.`amount` FROM `' . _DB_PREFIX_ . 'wk_gift_card` wgc
        LEFT JOIN `' . _DB_PREFIX_ . 'wk_gift_card_shop` wgcs ON (wgcs.`id` = wgc.`id` AND wgcs.`id_shop` = ' . (int) $idShop . ') WHERE wgc.`dynamic_price` = 0';
        $result = Db::getInstance()->executeS($sql);
        if ($result && count($result) > 0) {
            return $result;
        }

        return false;
    }
}
