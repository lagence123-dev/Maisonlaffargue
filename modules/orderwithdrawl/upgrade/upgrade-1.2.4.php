<?php
/**
 * Upgrade script for orderwithdrawl v1.2.4
 *
 * PrestaShop 1.7 can fail to resolve module mail templates reliably when
 * decision emails are sent through order-state send_email processing.
 * From 1.2.4 onward, approved/rejected mails are sent directly by the module
 * in hookActionOrderStatusPostUpdate(), so the automatic state emails must be
 * disabled for these two states.
 *
 * Historical note: upgrade-1.2.1.php originally set send_email=1 for the
 * approved/rejected states. That decision is revoked here because the module
 * took over mail dispatch. upgrade-1.2.1.php has since been corrected to
 * write send_email=0 directly, so the SQL below is only a safety net for
 * shops that ran an earlier revision of 1.2.1 before 1.2.4 landed.
 *
 * Style note: uses raw Db::execute() with pSQL()-safe integer casts to stay
 * consistent with the rest of the upgrade/ tree (see upgrade-1.2.1.php).
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_4($module)
{
    $db = Db::getInstance();
    $success = true;

    $stateKeys = [
        'ORDERWITHDRAWL_STATE_APPROVED_ID',
        'ORDERWITHDRAWL_STATE_REJECTED_ID',
    ];

    foreach ($stateKeys as $configKey) {
        $stateId = (int) Configuration::get($configKey);
        if ($stateId <= 0) {
            continue;
        }

        $success = $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_state`'
            . ' SET `send_email` = 0'
            . ' WHERE `id_order_state` = ' . $stateId
        ) && $success;
    }

    return $success;
}
