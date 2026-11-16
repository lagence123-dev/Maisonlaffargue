<?php

namespace MoneticoPaiement\Model;

use Db;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentDisplayMethod;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentMode;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentProtocol;
use PrestaShop\PrestaShop\Adapter\Entity\Context;

class MoneticoPaiementHelper
{

    /**
     * Fix insert tabs to DB
     * @return bool
     */
    public static function updateTabsName($prefix)
    {
        $sql = "UPDATE `" . pSQL($prefix) . "tab` SET `module`='MoneticoPaiement' WHERE  `module`= 'moneticopaiement'";
        return Db::getInstance()->execute($sql);
    }

    /**
     * @param $number
     * Display the Mac key
     *
     * @return string
     */
    public static function displayMacSecurity($number)
    {
        return substr($number, 0, 4) . "**********" . substr($number, -4);
    }

    /**
     * Return all EPT
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function getAllEpt($mac_hidden = true)
    {

        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('monetico_ept');

        /** @var false|array $epts False when there is no data in the table. */
        $epts = Db::getInstance()->executeS($sql);
        if ($epts === false) {
            $epts = [];
        }

        $payment_mode_class = new \ReflectionClass('MoneticoPaiement\Sdk\Utilities\Collections\PaymentMode');
        $pm_constants = $payment_mode_class->getConstants();
        $defaultLang = (int)\Configuration::get('PS_LANG_DEFAULT');

        $data_epts = [];
        foreach ($epts as $ept) {
            $data_epts[] = [
                'id' => $ept['id_ept'],
                'number' => $ept['ept_number'],
                'mac' => $mac_hidden ? self::displayMacSecurity($ept['ept_mac']) : $ept['ept_mac'],
                'payment_mode' => isset($pm_constants[$ept['ept_payment_mode']][$defaultLang]) ? $pm_constants[$ept['ept_payment_mode']][$defaultLang] : $pm_constants[$ept['ept_payment_mode']][PaymentMode::DEFAULT_LANGUAGE]
            ];
        }

        return $data_epts;
    }

    /**
     * Return an array with the excluded protocol to disable on the payment page.
     *
     * @param float $amount
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function getExcludedProtocol(float $amount): array
    {
        $sql = new \DbQuery();
        $sql->select('id_code_site, code_site_payment_method, code_site_active, code_site_amount_min, code_site_amount_max');
        $sql->from('monetico_code_site');
        $sql->where('(code_site_amount_min IS NOT NULL AND code_site_amount_min != "") OR (code_site_amount_max IS NOT NULL AND code_site_amount_max != "")');

        $code_sites = Db::getInstance()->executeS($sql);

        $excluded_protocols = [];
        foreach ($code_sites as $code_site) {
            if ($code_site['code_site_payment_method'] === 'cb') {
                continue;
            }

            if (!$code_site['code_site_active'] ||
                (isset($code_site['code_site_amount_min']) && $amount < $code_site['code_site_amount_min']) ||
                (isset($code_site['code_site_amount_max']) && $amount > $code_site['code_site_amount_max'])) {
                $excluded_protocols[] = $code_site['code_site_payment_method'];
            }
        }

        return $excluded_protocols;
    }

    /**
     * Return all Code Site
     *
     * @param bool $withAllOptions Tell whether to include the 3DS and options of the code site with the returned data
     *
     * @return array<array<string, mixed>>
     *
     * @throws \PrestaShopDatabaseException
     */
    public static function getAllCodeSites(bool $withAllOptions = false)
    {

        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('monetico_code_site');

        /** @var false|array $code_sites False when there is no data in the table. */
        $code_sites = Db::getInstance()->executeS($sql);
        if ($code_sites === false) {
            $code_sites = [];
        }

        $context = new Context();
        $lang = $context->getContext()->language->iso_code;
        $data_code_sites = [];
        $nb_max_length_display_name = 30;
        $payment_protocol = PaymentProtocol::PROTOCOL;
        foreach ($code_sites as $code_site) {
            // shortcuts if display name was too long
            $cs_display_name = $code_site['code_site_display_name'];
            $csdn_good_length = mb_strlen($cs_display_name) > $nb_max_length_display_name;
            $csdn_shortcuts = $csdn_good_length ? mb_substr($cs_display_name, 0, 30) . '...' : $cs_display_name;
            $code_site_payment_protocol = $payment_protocol[$code_site['code_site_payment_method']];

            $codeSiteData = [
                'id' => $code_site['id_code_site'],
                'code_site_ept' => self::getEptNumberFromId($code_site['code_site_ept_id']),
                'code_site_name' => $code_site['code_site_name'],
                'code_site_payment_method' => $code_site['code_site_payment_method'] ?
                    ($code_site_payment_protocol[$lang] ?? $code_site_payment_protocol[PaymentProtocol::DEFAULT_LANGUAGE])
                    : null,
                'code_site_payment_display_method' => ($code_site['code_site_payment_method'] === 'cb') ?
                    self::getCodeSiteDisplayMethod($code_site['id_code_site'], $lang) : null,
                'code_site_langue' => $code_site['code_site_langue'],
                'code_site_display_name' => $csdn_shortcuts,
                'code_site_active' => $code_site['code_site_active'],
                'code_site_sandbox' => $code_site['code_site_sandbox'],
                'code_site_express_payment' => ($code_site['code_site_payment_method'] === 'cb') ? $code_site['code_site_express_payment'] : null,
            ];

            if ($withAllOptions) {
                $codeSite = new MoneticoPaiementCodeSite($code_site['id_code_site']);
                $codeSiteData['3ds'] = $codeSite->get3DSV2();
                $codeSiteData['options'] = $codeSite->getOpts();
            }

            $data_code_sites[] = $codeSiteData;
        }

        return $data_code_sites;
    }

    public static function getAllIpaCodeSite()
    {
        $sql = new \DbQuery();
        $sql->select('id_code_site');
        $sql->from('monetico_code_site');
        $sql->where('`code_site_payment_method` = "ipa"');
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Return the display method of a Code Site
     *
     * @param $code_site_id
     * @param $lang
     *
     *
     * @return string
     */
    public static function getCodeSiteDisplayMethod($code_site_id, $lang)
    {
        $sql = new \DbQuery();
        $sql->select('value');
        $sql->from('monetico_code_site_opts');
        $sql->where('`code_site_id` = "' . (int)$code_site_id . '"');
        $sql->where(' `opt` = "code_site_payment_display_method"');

        $value = Db::getInstance()->getValue($sql);

        if (is_null($value)) {
            return null;
        }

        $default_language = PaymentDisplayMethod::DEFAULT_LANGUAGE;
        switch ($value) {
            case 'deported':
                $translated_value = PaymentDisplayMethod::deported[$lang] ?? PaymentDisplayMethod::deported[$default_language];
                break;
            case 'hostedfields':
                $translated_value = PaymentDisplayMethod::hostedfields[$lang] ?? PaymentDisplayMethod::hostedfields[$default_language];
                break;
            default:
                $translated_value = null;
        }

        return $translated_value;
    }

    /**
     * Return the EPT number of a EPT ID
     * @return string
     */
    public static function getEptNumberFromId($id)
    {
        $sql = new \DbQuery();
        $sql->select('ept_number');
        $sql->from('monetico_ept');
        $sql->where('id_ept = ' . (int)$id);

        $row = Db::getInstance()->getValue($sql);
        return $row ?? '';
    }

    /**
     * Return the EPT Mac of a EPT number
     * @return string
     */
    public static function getEptMacFromEptNumber($ept_number, $id = null)
    {
        $sql = new \DbQuery();
        $sql->select('ept_mac');
        $sql->from('monetico_ept');
        $sql->where('ept_number = "' . pSQL($ept_number) . '"');

        if (isset($id)) {
            $sql->where('id_ept != ' . (int)$id);
        }

        $row = Db::getInstance()->getValue($sql);

        return $row ?? '';
    }

    /**
     * Return the code site by name
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function getCodeSiteByName($code_societe_name)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('monetico_code_site');
        $sql->where('code_site_name = "' . pSQL($code_societe_name) . '"');

        return Db::getInstance()->getRow($sql);
    }

    /**
     * @param $ept_number
     *
     * @return array|bool|object|null
     */
    public static function getEptFromEptNumber($ept_number)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('monetico_ept');
        $sql->where('ept_number = "' . pSQL($ept_number) . '"');

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Return the last Code Site Id
     * @return int
     */
    public static function getLastCodeSiteId(): int
    {
        $sql = new \DbQuery();
        $sql->select('id_code_site');
        $sql->from('monetico_code_site');
        $sql->orderBy('id_code_site desc ');

        return (int)Db::getInstance()->getValue($sql);
    }

    /**
     * get count data from database
     * @return array|false|mysqli_result|PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getEptCount()
    {
        $sql = new \DbQuery();
        $sql->select('count(*)');
        $sql->from('monetico_ept');
        return (int)Db::getInstance()->getValue($sql);
    }

    /**
     * get count data from database
     * @return array|false|mysqli_result|PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getCodeSiteCount()
    {
        $sql = new \DbQuery();
        $sql->select('count(*)');
        $sql->from('monetico_code_site');
        return (int)Db::getInstance()->getValue($sql);
    }

    /**
     * get Code site by filter elements in options
     *
     * @param $array_filter
     * one filter is $key => $value
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function codeSiteOptionsFilter($array_filter)
    {
        if (count($array_filter) > 0) {
            $code_sites_ids = [];
            foreach ($array_filter as $filter_key => $filter_value) {

                $sql = new \DbQuery();
                $sql->select('code_site_id');
                $sql->from('monetico_code_site_opts');
                $sql->where(' `opt` = "' . pSQL($filter_key) . '"');
                $sql->where('`value` = "' . pSQL($filter_value) . '"');

                $options = Db::getInstance()->executeS($sql);
                $filter_ids = [];
                foreach ($options as $option) {
                    $filter_ids[] = $option['code_site_id'];
                }
                $code_sites_ids[] = $filter_ids;
            }

            $filter_ids = array_shift($code_sites_ids);
            // we intersect the result of each
            foreach ($code_sites_ids as $codes_sites_row) {
                $filter_ids = array_uintersect($filter_ids, $codes_sites_row, "strcasecmp");
            }

            // we return the request of all code site active with this ids
            return array_values($filter_ids);
        }
        return [];
    }

    /**
     * Return the code site by active and options
     *
     * @param $code_site_ids
     * @param $array_filter
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function codeSiteFilter($code_site_ids, $array_filter)
    {
        if (count($code_site_ids) === 0) {
            return [];
        }
        $code_sites_options_ids = self::codeSiteOptionsFilter($array_filter);
        $array_code_site_ids = [];
        if (count($code_sites_options_ids) > 0) {
            // construct the where in
            $code_site_where_in = implode(',', $code_sites_options_ids);
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from('monetico_code_site');
            $sql->where('id_code_site IN (' . pSQL($code_site_where_in) . ')');
            $sql->where('`code_site_active` = 1');
            $result = Db::getInstance()->executeS($sql);
            foreach ($result as $code_site) {
                if (in_array($code_site['id_code_site'], $code_site_ids)) {
                    $array_code_site_ids[] = $code_site['id_code_site'];
                }
            }
        }
        return $array_code_site_ids;
    }

    /**
     * get the number of a specific options key for a code site
     *
     * @param $code_site_id
     * @param $filter_key
     *
     * @return int
     *
     */
    public static function codeSiteOptionsFilterCount($code_site_id, $filter_key)
    {

        $sql = new \DbQuery();
        $sql->select('count(*)');
        $sql->from('monetico_code_site_opts');
        $sql->where('`code_site_id` = "' . pSQL($code_site_id) . '"');
        $sql->where(' `opt` = "' . pSQL($filter_key) . '"');

        return (int)Db::getInstance()->getValue($sql);
    }

    /**
     * get the number of a specific options key and value for a code site
     *
     * @param $code_site_id
     * @param $filter_key
     * @param $fitler_value
     *
     * @return int
     */
    public static function codeSiteOptionsHasValue($code_site_id, $filter_key, $fitler_value)
    {
        $sql = new \DbQuery();
        $sql->select('count(*)');
        $sql->from('monetico_code_site_opts');
        $sql->where('`code_site_id` = "' . (int)$code_site_id . '"');
        $sql->where(' `opt` = "' . pSQL($filter_key) . '"');
        $sql->where(' `value` = "' . pSQL($fitler_value) . '"');

        return (int)Db::getInstance()->getValue($sql);
    }


    /**
     * Filter code sites ids for optional filters
     *
     * @param $code_sites_ids
     * @param $array_filter
     */
    public static function codeSiteFilterOptional($code_sites_ids, $array_filter)
    {
        $new_codes_sites_ids = $code_sites_ids;
        if (count($array_filter)) {
            foreach ($array_filter as $filter_key => $filter_value) {
                $new_codes_sites_ids = [];
                foreach ($code_sites_ids as $code_sites) {
                    // if more than one filter country
                    if (self::codeSiteOptionsFilterCount($code_sites, $filter_key) > 0) {
                        // and if it's a restricted country
                        if (self::codeSiteOptionsHasValue($code_sites, $filter_key, $filter_value) > 0) {
                            $new_codes_sites_ids[] = $code_sites;
                        }
                    } else {
                        $new_codes_sites_ids[] = $code_sites;
                    }
                }
                $code_sites_ids = $new_codes_sites_ids;
            }

        }

        return $new_codes_sites_ids;
    }

    /**
     * Return if transaction already exist
     *
     * @param $id_cart
     *
     * @return int
     */
    public static function isDuplicateTransaction($id_cart)
    {
        $sql = new \DbQuery();
        $sql->select('count(*)');
        $sql->from('monetico_transaction');
        $sql->where(' `cart_id` = ' . (int)$id_cart);

        return (int)Db::getInstance()->getValue($sql);
    }

    /**
     * Return the transaction id for a cart id
     *
     * @param $id_cart
     *
     * @return array|bool|object|null
     */
    public static function getTransactionIdByCartId($id_cart)
    {
        $sql = new \DbQuery();
        $sql->select('id');
        $sql->from('monetico_transaction');
        $sql->where(' `cart_id` = ' . (int)$id_cart);
        return (int)Db::getInstance()->getValue($sql);
    }

    /**
     * Return the transaction by reference
     *
     * @param $order_ref
     *
     * @return array|bool|object|null
     */
    public static function getTransactionByOrderRef($order_ref)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('monetico_transaction');
        $sql->where(' `order_ref` = "' . pSQL($order_ref) . '"');
        return Db::getInstance()->getRow($sql);
    }

    /**
     * Return the sum amont for one order
     *
     * @param $order_id
     *
     * @return float
     */
    public static function getSumAmountForOneOrder($order_id)
    {
        $sql = new \DbQuery();
        $sql->select('sum(amount)');
        $sql->from('monetico_refund');
        $sql->where(' `order_id` = ' . (int)$order_id);
        return (float)Db::getInstance()->getValue($sql);
    }

    /**
     * Return all refunds for an order
     *
     * @param $order_id
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getRefundByOrderId($order_id)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('monetico_refund');
        $sql->where(' `order_id` = ' . (int)$order_id);
        $sql->orderBy('id DESC');
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Return the 3dsv2 constraint for the amount
     */
    public static function get3dsv2Constraint($code_site_id, $amount)
    {
        $sql = new \DbQuery();
        $sql->select('state');
        $sql->from('monetico_code_site_3dsv2');
        $sql->where(' `code_site_id` = ' . (int)$code_site_id);
        $sql->where(' `amount` <= ' . (float)$amount);
        $sql->orderBy('amount desc');
        $row = Db::getInstance()->getValue($sql);
        return $row ?? '';
    }

    /**
     * Return the current alias for a code site and a user
     *
     * @param $code_site_id
     * @param $user_id
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getAlias($code_site_id, $user_id)
    {
        $sql = new \DbQuery();
        $sql->select('alias');
        $sql->from('monetico_alias_bc');
        $sql->where(' `code_site_id` = ' . (int)$code_site_id);
        $sql->where(' `user_id` = ' . (int)$user_id);
        $row = Db::getInstance()->getValue($sql);
        return $row ?? '';
    }

    /**
     * Return the current alias ID for an alias
     *
     * @param $alias
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getAliasId($alias)
    {
        $sql = new \DbQuery();
        $sql->select('id');
        $sql->from('monetico_alias_bc');
        $sql->where(' `alias` = "' . pSQL($alias) . '"');
        $row = Db::getInstance()->getValue($sql);
        return $row ?? '';
    }

    /**
     * Return the current alias for a user
     *
     * @param $user_id
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getUsersAlias($user_id)
    {
        $sql = new \DbQuery();
        $sql->select('*');

        $sql->from('monetico_alias_bc');
        $sql->where(' `user_id` = ' . (int)$user_id);
        $sql->where(' `certified` = 1');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Remove unused alias for a user id
     *
     * @param $user_id
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function removeOthersAlias($user_id)
    {
        $request = '`user_id` = ' . (int)$user_id . ' and `certified` = 0';
        return Db::getInstance()->delete('monetico_alias_bc', $request);
    }

    /**
     * get code site ids filter by amount
     *
     * @param $amount
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function getCodeSiteIdsByAmount($amount)
    {

        // get all code site with amount_min is less than amount
        $sql = new \DbQuery();
        $sql->select('id_code_site');
        $sql->from('monetico_code_site');
        $sql->where('ROUND(`code_site_amount_min`,2) <=  ' . (float)$amount . ' OR `code_site_amount_min` is NULL');
        $sql->where('`code_site_active` = 1');
        $result_min_ids = Db::getInstance()->executeS($sql);

        if (!$result_min_ids || count($result_min_ids) === 0) {
            return [];
        }


        $data_result_min_ids = [];
        foreach ($result_min_ids as $result_min_id) {
            $data_result_min_ids[] = $result_min_id['id_code_site'];
        }

        // get all code site with amount_max is more than amount
        $sql = new \DbQuery();
        $sql->select('id_code_site');
        $sql->from('monetico_code_site');
        $sql->where('ROUND(`code_site_amount_max`,2) >=  ' . (float)$amount . ' OR `code_site_amount_max` is NULL');
        $sql->where('`code_site_active` = 1');
        $result_max_ids = Db::getInstance()->executeS($sql);

        if (!$result_max_ids || count($result_max_ids) === 0) {
            return [];
        }

        $data_result_max_ids = [];
        foreach ($result_max_ids as $result_max_id) {
            $data_result_max_ids[] = $result_max_id['id_code_site'];
        }

        return array_intersect($data_result_min_ids, $data_result_max_ids);
    }

    /**
     * @param $etp_id
     * @param $code_site_name
     *
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getCodeSiteCurrentPaymentMethod($etp_id, $code_site_name)
    {
        $sql = new \DbQuery();
        $sql->select('code_site_payment_method');
        $sql->from('monetico_code_site');
        $sql->where('code_site_etp_id = ' . (int)$etp_id);
        $sql->where('code_site_name = ' . pSQL($code_site_name));
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Check if code site with hostedfield on prod
     * @return false|int
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function hasCodeSiteWithHostedFieldProd()
    {
        // get all code site ids with hostedfield
        $code_site_ids = self::codeSiteOptionsFilter([
            'code_site_payment_display_method' => 'hostedfields'
        ]);

        $has_code_site_prod = false;
        foreach ($code_site_ids as $code_site_id) {
            $code_site = new MoneticoPaiementCodeSite($code_site_id);
            if ($code_site->isCodeSiteActive()) {
                $has_code_site_prod |= !$code_site->isCodeSiteSandbox();
            }
        }
        return $has_code_site_prod;

    }

    public static function hasCodeSiteWithIpaProd()
    {
        // get all code site ids with ipa
        $code_site_ids = self::getAllIpaCodeSite();
        $has_code_site_prod = false;
        foreach ($code_site_ids as $code_site_id) {
            $code_site = new MoneticoPaiementCodeSite($code_site_id['id_code_site']);
            if ($code_site->isCodeSiteActive()) {
                $has_code_site_prod |= !$code_site->isCodeSiteSandbox();
            }
        }
        return $has_code_site_prod;
    }

    /***
     * Checks if non-demo ept exists
     * @return bool
     * @throws \PrestaShopDatabaseException
     */
    public static function demoCanBeActivated($demo_ept_numbers): bool
    {
        $sql = new \DbQuery();
        $sql->select('count(*)');
        $sql->from('monetico_ept');
        $sql->where('ept_number NOT IN (' . pSQL($demo_ept_numbers) . ')');
        return (int)Db::getInstance()->getValue($sql) === 0;
    }

    /***
     * Get demo orders and carts IDs
     * @return array|bool
     * @throws \PrestaShopDatabaseException
     */
    public static function getDemoOrders($demo_ept_numbers, $ept_number = null)
    {
        $sql = new \DbQuery();
        $sql->select('o.id_order, o.id_cart');
        $sql->from('orders', 'o');
        $sql->innerJoin('monetico_transaction', 't', 't.cart_id = o.id_cart');
        $sql->innerJoin('monetico_code_site', 'cs', 'cs.id_code_site = t.code_site_id');
        $sql->innerJoin('monetico_ept', 'e', 'cs.code_site_ept_id = e.id_ept');

        if ($ept_number !== null) {
            $sql->where('e.ept_number = "' . pSQL($ept_number) . '"');
        } else {
            $sql->where('e.ept_number IN (' . pSQL($demo_ept_numbers) . ')');
        }
        return Db::getInstance()->executeS($sql);
    }

    /***
     * Gets demo EPT already created
     * @return array returns id_ept and ept_payment_mode
     * @throws \PrestaShopDatabaseException
     */
    public static function demoEptCreated($demo_ept_numbers): array
    {
        $sql = new \DbQuery();
        $sql->select('id_ept, ept_payment_mode');
        $sql->from('monetico_ept');
        $sql->where('ept_number IN (' . pSQL($demo_ept_numbers) . ')');
        $req = Db::getInstance()->executeS($sql);

        $ept_created = [];

        if ($req && count($req) > 0) {
            foreach ($req as $ept) {
                $ept_created[] = ['id' => $ept['id_ept'], 'payment_mode' => $ept['ept_payment_mode']];
            }
        }
        return $ept_created;
    }

    /**
     * @param $id_lang
     * @param $id_module
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getCustomerGroups($id_lang, $id_module)
    {
        $sql = new \DbQuery();
        $sql->select('g.id_group, l.name');
        $sql->from('group', 'g');
        $sql->innerJoin('group_lang', 'l', 'g.id_group = l.id_group');
        $sql->innerJoin('module_group', 'm', 'g.id_group = m.id_group');
        $sql->where('l.id_lang = "' . pSQL($id_lang) . '"');
        $sql->where('m.id_module = "' . pSQL($id_module) . '"');

        return Db::getInstance()->executeS($sql);
    }

}