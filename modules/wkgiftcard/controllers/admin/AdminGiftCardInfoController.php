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

class AdminGiftCardInfoController extends ModuleAdminController
{
    public $statuses_array = [];

    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'wk_gift_card_order_details';
        $this->list_id = 'id';
        $this->className = 'GiftCardOrderDetails';

        $this->_select = 'ord.total_paid_tax_incl as total_paid_tax_incl, a.id as temp_id_order,
        ord.reference as reference ,ord.payment as payment,ord.id_order as id_pdf, CONCAT(c.`firstname`," ",
        c.`lastname`) as customer, osl.`name` AS `osname`, os.`color`';
        $this->_join .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` ord ON (a.`id_order` = ord.`id_order`)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = ord.`current_state`)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`
            AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';
        // if (!Shop::isFeatureActive() || Shop::getContext() !== Shop::CONTEXT_SHOP) {
        $this->_select .= ',shp.`name` as wk_ps_shop_name';
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = a.`id_shop`)';
        // }
        if (Shop::getContext() != Shop::CONTEXT_ALL) {
            $shopIds = implode(',', (new PrestaShop\PrestaShop\Adapter\Shop\Context())->getContextListShopID(Shop::SHARE_ORDER));
            $this->_where .= ' AND a.`id_shop` IN (' . $shopIds . ')';
        }
        $this->_group = 'GROUP BY ord.`id_order`';
        // $this->_orderBy = 'id_order';

        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        if ($statuses) {
            foreach ($statuses as $status) {
                $this->statuses_array[$status['id_order_state']] = $status['name'];
            }
        }

        $this->identifier = 'id';
        $this->list_no_link = true;
        parent::__construct();
        $this->fields_list = [
            // 'id' => [
            //     'title' => $this->l('ID'),
            //     'align' => 'center',
            // ],
            'id_order' => [
                'title' => $this->l('Order ID'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'reference' => [
                'title' => $this->l('Reference'),
                'havingFilter' => true,
            ],
            'customer' => [
                'title' => $this->l('Customer'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'payment' => [
                'title' => $this->l('Payment'),
                'havingFilter' => true,
            ],

            'total_paid_tax_incl' => [
                'title' => $this->l('Total'),
                'align' => 'center',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
                'filter_type' => 'string',
            ],
            'osname' => [
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname',
            ],
            'id_pdf' => [
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
        ];
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['wk_ps_shop_name'] = [
                'title' => $this->l('Shop'),
                'align' => 'center',
                'havingFilter' => true,
            ];
        }
        $this->fields_list['temp_id_order'] = [
            'title' => $this->l('Action'),
            'align' => 'center',
            'remove_onclick' => true,
            'callback' => 'showViewButton',
            'search' => false,
        ];
        $this->_conf[40] = $this->l('Voucher mail successfully send');
    }

    public function showViewButton($tempIdOrder)
    {
        $objectCardAllInfo = new GiftCardOrderDetails($tempIdOrder);
        $idOrder = $objectCardAllInfo->id_order;
        $idCardRule = $objectCardAllInfo->id_card_rule;
        $cartRuleObj = new CartRule((int) $idCardRule);
        if ($idOrder) {
            if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
                $wkOrderLink = $this->context->link->getAdminLink('AdminOrders') .
                    '&id_order=' . (int) $idOrder . '&vieworder';
            } else {
                $sfRouteParams['route'] = 'admin_orders_view';
                $sfRouteParams['orderId'] = $idOrder;
                $wkOrderLink = $this->context->link->getAdminLink('AdminOrders', true, $sfRouteParams);
            }

            $this->context->smarty->assign(
                [
                    'order_link' => $wkOrderLink,
                    'order_id' => $idOrder,
                ]
            );
        }

        $this->context->smarty->assign(
            'view_href',
            self::$currentIndex . '&token=' . $this->token . '&viewwk_gift_card_order_details&id_order=' . $tempIdOrder
        );
        if ($cartRuleObj->active) {
            $this->context->smarty->assign(
                'edit_href',
                self::$currentIndex . '&token=' . $this->token . '&updatewk_gift_card_order_details&id_order=' . $idOrder
            );
        }

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/show_view_button.tpl'
        );
    }

    public static function setOrderCurrency($echo, $tr)
    {
        $order = new Order($tr['id_order']);

        return Tools::displayPrice($echo, (int) $order->id_currency);
    }

    public function printPDFIcons($idOrder, $tr)
    {
        static $validOrderState = [];

        $order = new Order($idOrder);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        if (!isset($validOrderState[$order->current_state])) {
            $validOrderState[$order->current_state] = Validate::isLoadedObject($order->getCurrentOrderState());
        }

        if (!$validOrderState[$order->current_state]) {
            return '';
        }

        $this->context->smarty->assign([
            'order' => $order,
            'tr' => $tr,
        ]);

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }

    public function renderView()
    {
        $id = Tools::getValue('id_order');
        if (($id = Tools::getValue('id_order'))
        && Tools::getIsset('viewwk_gift_card_order_details')
        && (!Tools::getValue('id_card_rule')
        || Tools::getValue('submitFilterwk_gift_card_order_details'))) {
            if (!Tools::isSubmit('submitFilterwk_gift_card_order_details')) {
                $this->processResetFilters();
            }
            $objectCardAllInfo = new GiftCardOrderDetails($id);
            $idOrder = $objectCardAllInfo->id_order;
            $this->table = 'wk_gift_card_order_details';
            $this->className = 'GiftCardOrderDetails';
            $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'cart_rule` cart ON (a.`id_card_rule` = cart.`id_cart_rule`)';
            $this->_select = 'a.`id_card_rule` as temp_voucher_id,
               a.`genrate_code` as genrate_code,
               a.`quantity`as quantity,
               a.`used` as used,
               a. `date_to` as date_to,
               a.`active` as active';
            $this->_where = ' AND a.`id_order` = ' . (int) $idOrder;
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->_where .= ' AND a.`id_shop` = ' . (int) $this->context->shop->id;
            }

            $this->_group = 'GROUP BY a.`id`';
            $this->identifier = 'id';
            $this->list_id = 'wk_gift_card_order_details';
            $this->fields_list = [
                'id' => [
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'havingFilter' => true,
                    'orderby' => false,
                ],
                'id_card_rule' => [
                    'title' => $this->l('Voucher ID'),
                    'align' => 'center',
                    'havingFilter' => true,
                    'class' => 'fixed-width-xs',
                    'orderby' => false,
                ],
                'genrate_code' => [
                    'title' => $this->l('Code'),
                    'havingFilter' => true,
                    'orderby' => false,
                ],
                'quantity' => [
                    'title' => $this->l('Quantity'),
                    'align' => 'center',
                    'havingFilter' => true,
                    'orderby' => false,
                ],
                'used' => [
                    'title' => $this->l('Use'),
                    'align' => 'center',
                    'havingFilter' => true,
                    'orderby' => false,
                ],
                'date_to' => [
                    'title' => $this->l('Expiration date'),
                    'type' => 'datetime',
                    'havingFilter' => true,
                    'callback' => 'formatDateDisplay',
                    'orderby' => false,
                ],
                'active' => [
                    'title' => $this->l('Status'),
                    'active' => 'status',
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => false,
                ],
                'temp_voucher_id' => [
                    'title' => 'Action',
                    'align' => 'center',
                    'callback' => 'showVoucherButton',
                    'search' => false,
                    'orderby' => false,
                ],
            ];

            self::$currentIndex = self::$currentIndex . '&id_order=' . $id . '&view_voucher=1&viewwk_gift_card_order_details';
            $this->context->smarty->assign(
                [
                    'current' => self::$currentIndex,
                ]
            );

            if (Tools::isSubmit('submitFilter')) {
                $this->processFilter();
            }

            if (Tools::isSubmit('submitResetwk_gift_card_order_details')) {
                $this->processResetFilters();
            }

            return parent::renderList();
        } else {
            return parent::renderView();
        }
    }

    public function formatDateDisplay($date)
    {
        return date('d/m/Y', strtotime($date));
    }

    public function showVoucherButton($temp_voucher_id)
    {
        $cart_link = $this->context->link->getAdminLink('AdminCartRules') . '&id_cart_rule=' . $temp_voucher_id .
        '&updatecart_rule';
        $this->context->smarty->assign('cart_link', $cart_link);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/show_voucher_button.tpl'
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    /**
     * Show button in header (Module configuration)
     *
     * @return void
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['view'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name,
            'desc' => $this->l('Module configuration'),
            'icon' => 'process-icon-cogs',
        ];
    }

    public function vouchersStatusSet($status)
    {
        $result = true;
        if ($id_vouchers_list = Tools::getValue('wk_gift_card_order_detailsBox')) {
            foreach ($id_vouchers_list as $voucher_list) {
                $objectCardAllInfo = new GiftCardOrderDetails($voucher_list);
                $objCartRule = new CartRule($objectCardAllInfo->id_card_rule);
                $objCartRule->active = $status;
                $objCartRule->update();
                $objectCardAllInfo->active = $status;
                $result &= $objectCardAllInfo->update();
            }
            $idOrder = Tools::getValue('id_order');
            if ($result) {
                $this->redirect_after = self::$currentIndex . '&viewwk_gift_card_order_details&id_order=' . $idOrder . '&conf=5&token=' . $this->token;
            } else {
                $this->errors[] = $this->l('An error occurred while updating the status.');
            }
        } else {
            $this->errors[] = $this->l('You must select at least one item to perform a bulk action.');
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitBulkdeletewk_gift_card_order_details')) {
            // for sublist
            if ($id_vouchers_list = Tools::getValue('wk_gift_card_order_detailsBox')) {
                foreach ($id_vouchers_list as $voucher_list) {
                    $objectCardAllInfo = new GiftCardOrderDetails($voucher_list);
                    $objectCardAllInfo->delete();
                }
            } else {
                $this->errors[] = $this->l('You must select at least one item to perform a bulk action.');
            }
        } elseif (Tools::isSubmit('submitBulkenableSelectionwk_gift_card_order_details')) {
            $this->vouchersStatusSet(1);
        } elseif (Tools::isSubmit('submitBulkdisableSelectionwk_gift_card_order_details')) {
            $this->vouchersStatusSet(0);
        } elseif (Tools::isSubmit('statuswk_gift_card_order_details')) {
            $id = Tools::getValue('id');
            if ($id) {
                $objectCardAllInfo = new GiftCardOrderDetails($id);
                $objCartRule = new CartRule($objectCardAllInfo->id_card_rule);
                if ($objectCardAllInfo->active) {
                    $objectCardAllInfo->active = 0;
                    $objCartRule->active = 0;
                } else {
                    $objectCardAllInfo->active = 1;
                    $objCartRule->active = 1;
                }
                if ($objectCardAllInfo->update()) {
                    $objCartRule->update();
                    $idOrder = Tools::getValue('id_order');
                    Tools::redirectAdmin(self::$currentIndex . '&viewwk_gift_card_order_details&id_order=' . $idOrder . '&conf=5&token=' . $this->token);
                } else {
                    $this->errors[] = $this->l('An error occurred while updating the status.');
                }
            }
        } elseif (Tools::isSubmit('updatewk_gift_card_order_details')) {
            $idOrder = Tools::getValue('id_order');
            $order = new Order((int) $idOrder);
            $idShop = $order->id_shop;
            $idLang = $order->id_lang;
            $moduleObj = new WkGiftCard();
            $moduleObj->sendMailToCustomer($idOrder, $idShop, $idLang);
            Tools::redirectAdmin(self::$currentIndex . '&conf=40&token=' . $this->token);
        }
        parent::postProcess();
    }

    public function ajaxProcessGetGiftProduct()
    {
        if (Tools::getValue('wk_token') == $this->context->controller->module->secure_key) {
            $query = Tools::getValue('q', false);
            $productId = Tools::getValue('product_id');
            if (!$query or $query == '' or Tools::strlen($query) < 1) {
                exit;
            }
            if ($pos = strpos($query, ' (ref:')) {
                $query = Tools::substr($query, 0, $pos);
            }

            $excludeIds = Tools::getValue('excludeIds', false);
            if ($excludeIds && $excludeIds != 'NaN') {
                $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
            } else {
                $excludeIds = '';
            }
            // Excluding downloadable products from packs because download from pack is not supported
            $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', true);
            $excludePacks = (bool) Tools::getValue('exclude_packs', true);

            $context = Context::getContext();
            $objectCardAllInfo = new GiftCardOrderDetails();

            // select product for giftcard product by using this function
            $items = $objectCardAllInfo->getSelectedProductForGiftProduct(
                $productId,
                $query,
                $excludeIds,
                $excludePacks,
                $excludeVirtuals
            );

            if ($items && ($excludeIds || strpos($_SERVER['HTTP_REFERER'], 'AdminScenes') !== false)) {
                foreach ($items as $item) {
                    echo trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|'
                    . (int) $item['id_product'] . "\n";
                }
            } elseif ($items) {
                // packs
                $results = [];
                foreach ($items as $item) {
                    $cover = Product::getCover($item['id_product']);
                    if ($cover) {
                        $image = str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                $item['id_image'],
                                ImageType::getFormattedName('home')
                            )
                        );
                    } else {
                        $imageRetriever = new ImageRetriever($this->context->link);
                        $image = $imageRetriever->getNoPictureImage($this->context->language)['bySize'][ImageType::getFormattedName('home')]['url'];
                    }
                    $product = [
                        'id' => (int) $item['id_product'],
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => $image,
                    ];
                    array_push($results, $product);
                }
                $results = array_values($results);
                echo json_encode($results);
            } else {
                exit(
                    json_encode(
                        [
                            'satatus' => 'failed',
                            'errors' => [$this->l('Not product found.')],
                            'data' => '',
                        ]
                    )
                );
            }
        } else {
            exit(
                json_encode(
                    [
                        'satatus' => 'failed',
                        'errors' => [$this->l('Invalid security token.')],
                        'data' => '',
                    ]
                )
            );
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/gift_product.css');
    }
}
