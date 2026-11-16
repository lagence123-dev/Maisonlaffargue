<?php

class BonthemeOverride extends Bontheme
{
    public function hookActionPresentOrder($params) {
        $params['presentedOrder']->appendClosure('products', function() use ($params) {
            $products = $params['presentedOrder']->getProducts();
            foreach ($products as &$product) {
                $product['available_later'] = Db::getInstance()->getValue('SELECT `available_later` FROM `' . _DB_PREFIX_ . 'product_lang` WHERE `id_product` = ' . $product['id_product'] . ' AND `id_lang` = ' . $this->context->language->id);
            }
            return $products;
        });
    }
}
