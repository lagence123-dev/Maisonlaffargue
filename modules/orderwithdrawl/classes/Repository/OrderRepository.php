<?php
/**
 * Repository abstraction around PrestaShop order lookups used by the
 * withdrawal flow.
 *
 * Concrete queries still run through Db / Order internally, but funneling
 * them through this class gives tests (and any future B3-B8 service) a seam
 * where they can stub responses without bootstrapping the full shop.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Repository;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;
use OrderWithdrawl\Order\OrderIdentifier;

class OrderRepository
{
    /**
     * Fetch an order row by the identifier a customer enters in the
     * withdrawal form. Numeric values are treated as PrestaShop order IDs
     * first, with a reference fallback so existing shops keep working.
     *
     * @return array<string, mixed>|null
     */
    public function findRowByIdentifier(string $identifier)
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        if (ctype_digit($identifier)) {
            $row = $this->findRowById((int) $identifier);
            if ($row !== null) {
                return $row;
            }
        }

        return $this->findRowByReference($identifier);
    }

    /**
     * Fetch an order row according to the admin-selected customer identifier
     * mode: numeric order id, public reference, or both.
     *
     * @return array<string, mixed>|null
     */
    public function findRowByIdentifierMode(string $identifier, string $mode)
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        $mode = OrderIdentifier::normalizeMode($mode);
        if ($mode === 'order_id') {
            return ctype_digit($identifier) ? $this->findRowById((int) $identifier) : null;
        }

        if ($mode === 'reference') {
            return $this->findRowByReference($identifier);
        }

        return $this->findRowByIdentifier($identifier);
    }

    /**
     * Fetch an order row (associative array) by its numeric order id.
     *
     * @return array<string, mixed>|null
     */
    public function findRowById(int $idOrder)
    {
        if ($idOrder <= 0 || !class_exists('Db', false)) {
            return null;
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_order` = ' . $idOrder;
        $row = \Db::getInstance()->getRow($sql);

        return is_array($row) ? $row : null;
    }

    /**
     * Fetch an order row (associative array) by its public reference.
     *
     * @return array<string, mixed>|null
     */
    public function findRowByReference(string $reference)
    {
        if ($reference === '' || !class_exists('Db', false)) {
            return null;
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . "orders` WHERE `reference` = '"
            . \pSQL($reference) . "'";
        $row = \Db::getInstance()->getRow($sql);

        return is_array($row) ? $row : null;
    }

    /**
     * Load an Order by primary key. Returns null when the id is invalid or the
     * loaded object is not usable, so callers never have to run
     * `Validate::isLoadedObject` themselves.
     */
    public function findById(int $idOrder)
    {
        if ($idOrder <= 0 || !class_exists('Order')) {
            return null;
        }

        $order = new \Order($idOrder);
        if (!$this->isLoadedOrderObject($order)) {
            return null;
        }

        return $order;
    }

    protected function isLoadedOrderObject($order): bool
    {
        if (!is_object($order)) {
            return false;
        }

        if (class_exists('Validate', false) && \Validate::isLoadedObject($order)) {
            return true;
        }

        if (isset($order->id)) {
            return (int) $order->id > 0;
        }

        if (method_exists($order, '__get')) {
            return (int) $order->__get('id') > 0;
        }

        return false;
    }

    /**
     * Return true if the given order already has an `OrderHistory` row with
     * the supplied state id. Used to block duplicate withdrawal submissions.
     */
    public function hasOrderStateHistoryEntry(int $idOrder, int $idOrderState): bool
    {
        if ($idOrder <= 0 || $idOrderState <= 0 || !class_exists('Db', false)) {
            return false;
        }

        $sql = 'SELECT `id_order_history` FROM `' . _DB_PREFIX_ . 'order_history`'
            . ' WHERE `id_order` = ' . $idOrder
            . ' AND `id_order_state` = ' . $idOrderState;
        $value = \Db::getInstance()->getValue($sql);

        return $value !== false && $value !== null && (int) $value > 0;
    }

    public function acquireWithdrawalLock(int $idOrder, int $timeoutSeconds = 5): bool
    {
        if ($idOrder <= 0 || !class_exists('Db', false)) {
            return false;
        }

        $timeoutSeconds = max(0, min(30, $timeoutSeconds));
        $sql = "SELECT GET_LOCK('" . \pSQL($this->getWithdrawalLockName($idOrder)) . "', " . $timeoutSeconds . ')';
        $value = \Db::getInstance()->getValue($sql);

        return (string) $value === '1';
    }

    public function releaseWithdrawalLock(int $idOrder)
    {
        if ($idOrder <= 0 || !class_exists('Db', false)) {
            return;
        }

        $sql = "SELECT RELEASE_LOCK('" . \pSQL($this->getWithdrawalLockName($idOrder)) . "')";
        \Db::getInstance()->getValue($sql);
    }

    /**
     * Return public-safe order item details for the withdrawal product
     * selection step. Customer data, prices and addresses are intentionally
     * excluded.
     *
     * @return list<array{id_order_detail:int, name:string, reference:string, quantity:int}>
     */
    public function findProductsByOrderId(int $idOrder): array
    {
        $order = $this->findById($idOrder);
        if ($order === null || !method_exists($order, 'getProducts')) {
            return [];
        }

        $rawProducts = $order->getProducts();
        if (!is_array($rawProducts)) {
            return [];
        }

        $products = [];
        foreach ($rawProducts as $rawProduct) {
            if (!is_array($rawProduct)) {
                continue;
            }

            $idOrderDetail = (int) ($rawProduct['id_order_detail'] ?? 0);
            if ($idOrderDetail <= 0) {
                continue;
            }

            if ($this->isProductExcluded($rawProduct)) {
                continue;
            }

            $name = $this->normaliseProductText((string) ($rawProduct['product_name'] ?? ''), 255);
            if ($name === '') {
                continue;
            }

            $reference = (string) ($rawProduct['product_reference'] ?? '');
            if ($reference === '' && isset($rawProduct['product_attribute_reference'])) {
                $reference = (string) $rawProduct['product_attribute_reference'];
            }

            $quantity = (int) ($rawProduct['product_quantity'] ?? 0);
            if ($quantity < 1) {
                $quantity = 1;
            }

            $products[] = [
                'id_order_detail' => $idOrderDetail,
                'name' => $name,
                'reference' => $this->normaliseProductText($reference, 128),
                'quantity' => $quantity,
            ];
        }

        return $products;
    }

    /**
     * @param array<string, mixed> $rawProduct
     */
    private function isProductExcluded(array $rawProduct): bool
    {
        $excludedProductIds = $this->getConfiguredIdList(OrderWithdrawlConfig::EXCLUDED_PRODUCT_IDS);
        $idProduct = $this->getRawProductId($rawProduct);
        if ($idProduct > 0 && isset($excludedProductIds[$idProduct])) {
            return true;
        }

        $excludedCategoryIds = $this->getConfiguredIdList(OrderWithdrawlConfig::EXCLUDED_CATEGORY_IDS);
        if ($excludedCategoryIds !== []) {
            foreach ($this->getRawProductCategoryIds($rawProduct, $idProduct) as $idCategory) {
                if (isset($excludedCategoryIds[$idCategory])) {
                    return true;
                }
            }
        }

        return $this->shouldExcludeCustomizedProducts() && $this->hasCustomizationData($rawProduct);
    }

    /**
     * @return array<int, true>
     */
    private function getConfiguredIdList(string $key): array
    {
        if (!class_exists('Configuration', false)) {
            return [];
        }

        $value = (string) \Configuration::get($key, null, null, null, '');

        $ids = [];
        foreach (preg_split('/[,\s]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $rawId) {
            $id = (int) $rawId;
            if ($id > 0) {
                $ids[$id] = true;
            }
        }

        return $ids;
    }

    private function shouldExcludeCustomizedProducts(): bool
    {
        if (!class_exists('Configuration', false)) {
            return false;
        }

        return (bool) \Configuration::get(
            OrderWithdrawlConfig::EXCLUDE_CUSTOMIZED_PRODUCTS,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_EXCLUDE_CUSTOMIZED_PRODUCTS
        );
    }

    /**
     * @param array<string, mixed> $rawProduct
     */
    private function getRawProductId(array $rawProduct): int
    {
        foreach (['product_id', 'id_product'] as $key) {
            if (isset($rawProduct[$key]) && (int) $rawProduct[$key] > 0) {
                return (int) $rawProduct[$key];
            }
        }

        return 0;
    }

    /**
     * @param array<string, mixed> $rawProduct
     *
     * @return list<int>
     */
    private function getRawProductCategoryIds(array $rawProduct, int $idProduct): array
    {
        $ids = [];
        foreach (['id_category_default', 'product_id_category_default'] as $key) {
            if (isset($rawProduct[$key]) && (int) $rawProduct[$key] > 0) {
                $ids[(int) $rawProduct[$key]] = true;
            }
        }

        if (isset($rawProduct['category_ids'])) {
            $categoryIds = is_array($rawProduct['category_ids'])
                ? $rawProduct['category_ids']
                : preg_split('/[,\s]+/', (string) $rawProduct['category_ids'], -1, PREG_SPLIT_NO_EMPTY);
            foreach ($categoryIds ?: [] as $rawId) {
                $id = (int) $rawId;
                if ($id > 0) {
                    $ids[$id] = true;
                }
            }
        }

        if ($idProduct > 0 && class_exists('Product') && method_exists('Product', 'getProductCategories')) {
            try {
                $productCategoryIds = \Product::getProductCategories($idProduct);
            } catch (\Throwable $e) {
                $productCategoryIds = [];
            }
            if (is_array($productCategoryIds)) {
                foreach ($productCategoryIds as $rawId) {
                    $id = (int) $rawId;
                    if ($id > 0) {
                        $ids[$id] = true;
                    }
                }
            }
        }

        return array_keys($ids);
    }

    /**
     * @param array<string, mixed> $rawProduct
     */
    private function hasCustomizationData(array $rawProduct): bool
    {
        foreach (['id_customization', 'customization_id', 'customization_quantity_total'] as $key) {
            if (isset($rawProduct[$key]) && (int) $rawProduct[$key] > 0) {
                return true;
            }
        }

        foreach (['customizedDatas', 'customizations', 'customization'] as $key) {
            if (!empty($rawProduct[$key])) {
                return true;
            }
        }

        return false;
    }

    protected function normaliseProductText(string $value, int $maxLength): string
    {
        $normalised = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $value);
        $stripped = strip_tags($normalised);
        $collapsed = preg_replace('/\s+/u', ' ', $stripped);
        if (!is_string($collapsed)) {
            $collapsed = $stripped;
        }

        if (class_exists('Tools', false)) {
            return \Tools::substr(trim($collapsed), 0, $maxLength);
        }

        return substr(trim($collapsed), 0, $maxLength);
    }

    protected function getWithdrawalLockName(int $idOrder): string
    {
        return 'orderwithdrawl_withdrawal_' . $idOrder;
    }
}
