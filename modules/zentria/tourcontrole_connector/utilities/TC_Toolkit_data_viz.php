<?php
/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Toolkit scripts class, implements all data retrieval for data viz, based on date ranges
 *
 * @since 1.3.0
 * @author      zentria
 */
class TC_Toolkit_data_viz
{
    /**
     * Get number of carts by day with custom date range
     *
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getNumberOfCarts($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE_FORMAT(DATE(c.date_add), \'%Y-%m-%d\') date');
        $sql->select('COUNT(DISTINCT c.id_cart) AS number_carts');
        $sql->from('cart', 'c');
        $sql->leftJoin('cart_product', 'cp', 'cp.id_cart = c.id_cart AND cp.quantity > 0');
        $sql->where('cp.id_cart IS NOT NULL');
        $sql->where('c.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('c.date_add >= "' . pSQL($date_start) . '"');
        $sql->where('c.date_add <= "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(c.date_add)');
        $sql->orderBy('DATE(c.date_add)');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get carts amount by with custom date range
     *
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getCartsAmount($date_start, $date_end)
    {
        $id_shop = Context::getContext()->shop->id;

        // Get sum of not ordered carts, consider cart rules & products discount
        // Ignore carts with 0 products
        $carts_not_ordered = new DbQuery();
        $carts_not_ordered->select('c.id_cart, DATE(c.date_add) AS `date`, SUM(cp.quantity * IF(pas.price != 0, pas.price, ps.price)) - IFNULL(cr.reduction_amount, 0) AS carts_sum, SUM(cp.quantity) AS nb_products');
        $carts_not_ordered->from('cart', 'c');

        $carts_not_ordered->innerJoin('cart_product', 'cp', 'cp.id_cart = c.id_cart');
        $carts_not_ordered->leftJoin('product_shop', 'ps', 'ps.id_product = cp.id_product AND ps.id_shop = c.id_shop');
        $carts_not_ordered->leftJoin('product_attribute_shop', 'pas', 'pas.id_product_attribute = cp.id_product_attribute AND pas.id_shop = c.id_shop');
        $carts_not_ordered->leftJoin('orders', 'o', 'o.id_cart = c.id_cart');
        $carts_not_ordered->leftJoin('cart_cart_rule', 'ccr', 'ccr.id_cart = cp.id_cart');
        $carts_not_ordered->leftJoin('cart_rule', 'cr', 'cr.id_cart_rule = ccr.id_cart_rule');

        $carts_not_ordered->where('c.id_shop = ' . (int) $id_shop);
        $carts_not_ordered->where('c.date_add >= "' . pSQL($date_start) . '"');
        $carts_not_ordered->where('c.date_add < "' . pSQL($date_end) . '"');
        $carts_not_ordered->where('o.id_order IS NULL');

        $carts_not_ordered->groupBy('c.id_cart');
        $carts_not_ordered->having('nb_products > 0');
        $sum_carts_not_ordered_by_day = Db::getInstance()->executeS($carts_not_ordered);

        // Get sum of ordered carts, consider cart rules & products discount
        $carts_ordered = new DbQuery();
        $carts_ordered->select('
            c.id_cart,
            DATE(c.date_add) AS date,
            SUM(
                (od.product_quantity * od.unit_price_tax_excl - od.reduction_amount_tax_excl)
                * (1 - od.reduction_percent / 100)
            ) - IFNULL(ocr.value_tax_excl, 0) AS carts_sum
        ');
        $carts_ordered->from('cart', 'c');

        $carts_ordered->innerJoin('orders', 'o', 'o.id_cart = c.id_cart');
        $carts_ordered->innerJoin('order_detail', 'od', 'od.id_order = o.id_order');
        $carts_ordered->leftJoin('cart_cart_rule', 'ccr', 'ccr.id_cart = c.id_cart');
        $carts_ordered->leftJoin('order_cart_rule', 'ocr', 'ocr.id_order = od.id_order AND ocr.id_cart_rule = ccr.id_cart_rule');

        $carts_ordered->where('c.id_shop = ' . (int) $id_shop);
        $carts_ordered->where('c.date_add >= "' . pSQL($date_start) . '"');
        $carts_ordered->where('c.date_add < "' . pSQL($date_end) . '"');

        $carts_ordered->groupBy('c.id_cart');
        $sum_carts_ordered_by_day = Db::getInstance()->executeS($carts_ordered);

        // Merge not ordered & ordered carts sum
        $sum_carts_by_day = array_merge($sum_carts_not_ordered_by_day, $sum_carts_ordered_by_day);

        // Final array of our computed carts average by date
        $computedAverageCartsByDate = [];

        // Get number of carts & sum of carts by day
        foreach ($sum_carts_by_day as $cart) {
            $date = $cart['date'];

            // Initialize date item with 0 value if not exist
            if (!isset($computedAverageCartsByDate[$date])) {
                $computedAverageCartsByDate[$date] = [
                    'date' => $date,
                    'carts_amount' => 0,
                ];
            }

            // Increase $computedAverageCartsByDate with sum
            $computedAverageCartsByDate[$date]['carts_amount'] += $cart['carts_sum'];
        }

        return $computedAverageCartsByDate;
    }
    /**
     * Get revenue data by period with custom date range
     *
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getDayRevenue($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') AS date');
        $sql->select('ROUND(SUM(o.total_paid_tax_incl), 2) AS day_revenue');
        $sql->from('orders', 'o');
        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.valid = 1');
        $sql->where('o.date_add BETWEEN "' . pSQL($date_start) . '" AND "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add)');
        $sql->orderBy('DATE(o.date_add)');
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all data about orders with custom date range
     *
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getNumberOfOrders($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') date');
        $sql->select('COUNT(*) number_orders');
        $sql->from('orders', 'o');
        $sql->where('o.valid = 1');
        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.date_add BETWEEN "' . pSQL($date_start) . '" AND "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add)');
        $sql->orderBy('DATE(o.date_add)');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get count of orders from existing customers with custom date range
     * An existing customer is one who has at least one order before the current order
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersFromExistingCustomers($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') AS date');
        $sql->select('COUNT(DISTINCT o.id_order) AS orders_from_existing_customers');
        $sql->from('orders', 'o');
        $sql->innerJoin('orders', 'o2', 'o2.id_customer = o.id_customer AND o2.date_add < o.date_add');

        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.valid = 1');
        $sql->where('o.date_add BETWEEN "' . pSQL($date_start) . '" AND "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add)');
        $sql->orderBy('DATE(o.date_add)');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get count of orders from new customers with custom date range
     * A new customer is one who has no previous orders before the current order
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersFromNewCustomers($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') AS date');
        $sql->select('COUNT(DISTINCT o.id_order) AS orders_from_new_customers');
        $sql->from('orders', 'o');
        $sql->leftJoin('orders', 'o2', 'o2.id_customer = o.id_customer AND o2.date_add < o.date_add AND o2.id_shop = o.id_shop');

        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.valid = 1');
        $sql->where('o.date_add BETWEEN "' . pSQL($date_start) . '" AND "' . pSQL($date_end) . '"');
        $sql->where('o2.id_order IS NULL');
        $sql->groupBy('DATE(o.date_add)');
        $sql->orderBy('DATE(o.date_add)');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all data about products ordered with custom date range
     *
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getNumberOfProductsOrdered($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') date');
        $sql->select('SUM(od.product_quantity) AS number_products_ordered');
        $sql->from('orders', 'o');

        $sql->innerJoin('order_detail', 'od', 'o.id_order = od.id_order');

        $sql->where('o.valid = 1');
        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.date_add >= "' . pSQL($date_start) . '"');
        $sql->where('o.date_add <= "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add)');
        $sql->orderBy('DATE(o.date_add)');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get number of orders for the day for which customers have placed 1 orders
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersPerCustomer1($date_start, $date_end)
    {
        $sql = 'SELECT 
                DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') date,
                COUNT(*) AS orders_per_customer_1
            FROM ' . _DB_PREFIX_ . 'orders o
            JOIN (
                SELECT o1.id_order, o1.id_customer, o1.date_add, o1.id_shop
                FROM ' . _DB_PREFIX_ . 'orders o1
                JOIN ' . _DB_PREFIX_ . 'orders o2
                   ON o2.id_customer = o1.id_customer
                   AND o2.date_add <= o1.date_add
                   AND o2.id_shop = o1.id_shop
                   AND o2.valid = o1.valid
                GROUP BY o1.id_order, o1.id_customer
                HAVING COUNT(o2.id_order) = 1
            ) AS order_num
                ON o.id_order = order_num.id_order
            WHERE o.valid = 1 AND o.id_shop = ' . Context::getContext()->shop->id . ' AND o.date_add >= "' . pSQL($date_start) . '" AND o.date_add <= "' . pSQL($date_end) . '"
            GROUP BY DATE(o.date_add)
            ORDER BY DATE(o.date_add);';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get number of orders for the day for which customers have placed 2 orders
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersPerCustomer2($date_start, $date_end)
    {
        $sql = 'SELECT 
                DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') date,
                COUNT(*) AS orders_per_customer_2
            FROM ' . _DB_PREFIX_ . 'orders o
            JOIN (
                SELECT o1.id_order, o1.id_customer, o1.date_add, o1.id_shop
                FROM ' . _DB_PREFIX_ . 'orders o1
                JOIN ' . _DB_PREFIX_ . 'orders o2
                   ON o2.id_customer = o1.id_customer
                   AND o2.date_add <= o1.date_add
                   AND o2.id_shop = o1.id_shop
                   AND o2.valid = o1.valid
                GROUP BY o1.id_order, o1.id_customer
                HAVING COUNT(o2.id_order) = 2
            ) AS order_num
                ON o.id_order = order_num.id_order
            WHERE o.valid = 1 AND o.id_shop = ' . Context::getContext()->shop->id . ' AND o.date_add >= "' . pSQL($date_start) . '" AND o.date_add <= "' . pSQL($date_end) . '"
            GROUP BY DATE(o.date_add)
            ORDER BY DATE(o.date_add);';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get number of orders for the day for which customers have placed 3 orders
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersPerCustomer3($date_start, $date_end)
    {
        $sql = 'SELECT 
                DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') date,
                COUNT(*) AS orders_per_customer_3
            FROM ' . _DB_PREFIX_ . 'orders o
            JOIN (
                SELECT o1.id_order, o1.id_customer, o1.date_add, o1.id_shop
                FROM ' . _DB_PREFIX_ . 'orders o1
                JOIN ' . _DB_PREFIX_ . 'orders o2
                   ON o2.id_customer = o1.id_customer
                   AND o2.date_add <= o1.date_add
                   AND o2.id_shop = o1.id_shop
                   AND o2.valid = o1.valid
                GROUP BY o1.id_order, o1.id_customer
                HAVING COUNT(o2.id_order) = 3
            ) AS order_num
                ON o.id_order = order_num.id_order
            WHERE o.valid = 1 AND o.id_shop = ' . Context::getContext()->shop->id . ' AND o.date_add >= "' . pSQL($date_start) . '" AND o.date_add <= "' . pSQL($date_end) . '"
            GROUP BY DATE(o.date_add)
            ORDER BY DATE(o.date_add);';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get number of orders for the day for which customers have placed 4 or more orders
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersPerCustomer4($date_start, $date_end)
    {
        $sql = 'SELECT 
                DATE_FORMAT(DATE(o.date_add), \'%Y-%m-%d\') date,
                COUNT(*) AS orders_per_customer_4
            FROM ' . _DB_PREFIX_ . 'orders o
            JOIN (
                SELECT o1.id_order, o1.id_customer, o1.date_add, o1.id_shop
                FROM ' . _DB_PREFIX_ . 'orders o1
                JOIN ' . _DB_PREFIX_ . 'orders o2
                   ON o2.id_customer = o1.id_customer
                   AND o2.date_add <= o1.date_add
                   AND o2.id_shop = o1.id_shop
                   AND o2.valid = o1.valid
                GROUP BY o1.id_order, o1.id_customer
                HAVING COUNT(o2.id_order) >= 4
            ) AS order_num
                ON o.id_order = order_num.id_order
            WHERE o.valid = 1 AND o.id_shop = ' . Context::getContext()->shop->id . ' AND o.date_add >= "' . pSQL($date_start) . '" AND o.date_add <= "' . pSQL($date_end) . '"
            GROUP BY DATE(o.date_add)
            ORDER BY DATE(o.date_add);';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get best products by sales quantity and group by date
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getProductsBestSalesQuantity($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE(o.date_add) `date`, od.product_id, SUM(od.product_quantity) quantity');
        $sql->from('orders', 'o');
        $sql->innerJoin('order_detail', 'od', 'o.id_order = od.id_order');
        $sql->where('o.valid = 1');
        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.date_add >= "' . pSQL($date_start) . '"');
        $sql->where('o.date_add <= "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add), od.product_id');
        $sql->orderBy('DATE(o.date_add), quantity DESC, od.product_id');
        $bestProductsSalesQty = Db::getInstance()->executeS($sql);

        return self::computeBestSalesAsArray($bestProductsSalesQty, 'products_best_sales_quantity', 'quantity');
    }

    /**
     * Get best products by sales amount and group by date
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getProductsBestSalesAmount($date_start, $date_end)
    {
        $sql = new DbQuery();
        $sql->select('DATE(o.date_add) `date`, od.product_id, SUM((od.unit_price_tax_incl * od.product_quantity) - od.reduction_amount) amount');
        $sql->from('orders', 'o');
        $sql->innerJoin('order_detail', 'od', 'o.id_order = od.id_order');
        $sql->where('o.valid = 1');
        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.date_add >= "' . pSQL($date_start) . '"');
        $sql->where('o.date_add <= "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add), od.product_id');
        $sql->orderBy('DATE(o.date_add), amount DESC, od.product_id');
        $bestProductsSalesAmount = Db::getInstance()->executeS($sql);

        return self::computeBestSalesAsArray($bestProductsSalesAmount, 'products_best_sales_amount', 'amount');
    }

    /**
     * Compute best sales products to structured array grouped by day
     *
     * @param array $bestProducts Array of product to compute
     * @param string $dataviz_name Name of key containing all data about products
     * @param string $dataviz_value_name Name of key containing value
     * @return array
     */
    private static function computeBestSalesAsArray($bestProducts, $dataviz_name, $dataviz_value_name)
    {
        $bestSalesArray = [];

        if (empty($bestProducts)) {
            return $bestSalesArray;
        }

        // Get Ids of products
        $id_products = array_unique(array_column($bestProducts, 'product_id'));

        $sql = new DbQuery();
        $sql->select('id_product, name');
        $sql->from('product_lang');
        $sql->where('id_lang = ' . Configuration::get('PS_LANG_DEFAULT'));
        $sql->where('id_product IN (' . implode(',', $id_products) . ')');
        $products_lang = Db::getInstance()->executeS($sql);

        // Clean array to set id_product as key and name as value
        $product_names = array_column($products_lang, 'name', 'id_product');

        // Parse our products to compute array
        foreach ($bestProducts as $products_row) {
            $date = $products_row['date'];
            $id_product = (int) $products_row['product_id'];

            // Init date entry if not exist
            if (!isset($bestSalesArray[$date])) {
                $bestSalesArray[$date] = [
                    'date' => $date,
                    $dataviz_name => [],
                ];
            }

            // Limit number of products per day to 5
            if (count($bestSalesArray[$date][$dataviz_name]) >= 5) {
                continue;
            }

            // Setup our final array
            $bestSalesArray[$date][$dataviz_name][$id_product] = [
                'name' => isset($product_names[$id_product]) ? $product_names[$id_product] : 'N/C',
                $dataviz_value_name => $products_row[$dataviz_value_name],
            ];
        }

        return $bestSalesArray;
    }

    /**
     * Get best carriers used in orders with amount of order for each and group by date
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersPerCarrier($date_start, $date_end)
    {
        // Get all orders numbers per carrier per day order by number DESC
        $sql = new DbQuery();
        $sql->select('DATE(o.date_add) AS date, o.id_carrier, COUNT(*) AS orders_per_carrier');
        $sql->from('orders', 'o');
        $sql->where('o.valid = 1');
        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.date_add >= "' . pSQL($date_start) . '"');
        $sql->where('o.date_add <= "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add), o.id_carrier');
        $sql->orderBy('DATE(o.date_add), orders_per_carrier DESC, o.id_carrier ASC');
        $orders_per_carrier_by_day = Db::getInstance()->executeS($sql);

        // Get all carriers names
        // We dont do by SQL because before 1.7 carrier name is in carrier table, and after name is on carrier_lang table
        // Ignore carriers deleted
        $carrierNames = Carrier::getCarriers((int) Context::getContext()->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
        // Associate names to an id_carrier
        $nameMap = array_column($carrierNames, 'name', 'id_carrier');

        $computed_top_5_orders_carrier_by_day = [];
        $dataviz_name = 'orders_per_carrier';
        // Compute final structured array with our orders numbers by day and by carriers
        foreach ($orders_per_carrier_by_day as $carrier_orders) {
            $date = $carrier_orders['date'];
            $id_carrier = $carrier_orders['id_carrier'];

            // Init date entry if not exist
            if (!isset($computed_top_5_orders_carrier_by_day[$date][$dataviz_name])) {
                $computed_top_5_orders_carrier_by_day[$date] = ['date' => $date, $dataviz_name => []];
            }

            // Limit to TOP 5 Carriers per day, 5 Carriers + 1 Date, so limit a 6 items per array
            if (count($computed_top_5_orders_carrier_by_day[$date][$dataviz_name]) >= 5) {
                continue;
            }

            // If we dont know carrier name, ignore and continue
            if (!isset($nameMap[$id_carrier])) {
                continue;
            }

            // Add carrier name and quantity for current day
            $computed_top_5_orders_carrier_by_day[$date][$dataviz_name][$id_carrier] = [
                'name' => $nameMap[$id_carrier],
                'quantity' => (int) $carrier_orders[$dataviz_name],
            ];
        }

        return $computed_top_5_orders_carrier_by_day;
    }

    /**
     * Get best payment method used in orders with amount of order for each and group by date
     *
     * @param string $date_start Start date (Y-m-d H:i:s format)
     * @param string $date_end End date (Y-m-d H:i:s format)
     * @return array all data to get
     */
    public static function getWebsiteDataViz_getOrdersPerPaymentMethod($date_start, $date_end)
    {
        // Get all orders numbers per payment method per day order by number DESC
        $sql = new DbQuery();
        $sql->select('DATE(o.date_add) AS date, o.payment, COUNT(*) AS orders_per_payment_method');
        $sql->from('orders', 'o');
        $sql->where('o.valid = 1');
        $sql->where('o.id_shop = ' . (int) Context::getContext()->shop->id);
        $sql->where('o.date_add >= "' . pSQL($date_start) . '"');
        $sql->where('o.date_add <= "' . pSQL($date_end) . '"');
        $sql->groupBy('DATE(o.date_add), o.payment');
        $sql->orderBy('DATE(o.date_add), orders_per_payment_method DESC, o.payment ASC');
        $orders_per_payment_method_by_day = Db::getInstance()->executeS($sql);

        $computed_top_5_orders_payment_method_by_day = [];
        $dataviz_name = 'orders_per_payment_method';
        // Compute final structured array with our orders numbers by day and by payment method
        foreach ($orders_per_payment_method_by_day as $payment_orders) {
            $date = $payment_orders['date'];
            $payment_name = $payment_orders['payment'] ?: 'N/A';

            // Init date entry if not exist
            if (!isset($computed_top_5_orders_payment_method_by_day[$date][$dataviz_name])) {
                $computed_top_5_orders_payment_method_by_day[$date] = ['date' => $date, $dataviz_name => []];
            }

            // Limit to TOP 5 payment method per day, 5 payment method + 1 Date, so limit a 6 items per array
            if (count($computed_top_5_orders_payment_method_by_day[$date][$dataviz_name]) >= 5) {
                continue;
            }

            // Add payment method name and quantity for current day
            $computed_top_5_orders_payment_method_by_day[$date][$dataviz_name][$payment_name] = [
                'name' => $payment_name,
                'quantity' => (int) $payment_orders[$dataviz_name],
            ];
        }

        return $computed_top_5_orders_payment_method_by_day;
    }
}
