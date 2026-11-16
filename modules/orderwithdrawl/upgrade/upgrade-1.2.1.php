<?php
/**
 * Upgrade script for orderwithdrawl v1.2.1
 *
 * Fixes order state configuration for withdrawal states:
 * - State "Widerruf beantragt":         send_email=0, template=''
 *   (email sent by form controller via withdrawl_confirm, not on status change)
 * - State "Widerrufsanfrage bewilligt":  send_email=0, template='withdrawl_approved'
 * - State "Widerrufsanfrage abgelehnt":  send_email=0, template='withdrawl_rejected'
 *
 * History / rationale for send_email=0 on the decision states:
 *   Earlier revisions of this script set send_email=1 for approved/rejected.
 *   PrestaShop 1.7 cannot always resolve module-scoped mail templates when
 *   send_email is triggered from native state-transition processing.
 *   Since 1.2.4 the decision emails are dispatched directly from the module
 *   in hookActionOrderStatusPostUpdate(), so the automatic state emails must
 *   stay off. upgrade-1.2.4.php re-enforces send_email=0 for shops that ran
 *   any earlier 1.2.1 revision; keeping the authoritative value here ensures
 *   fresh installs and partial upgrades converge on the same state.
 *
 * PS finds module mail templates automatically via Mail::getTemplateBasePath() fallback.
 *
 * Two-layer approach for reliability:
 * 1. ORM layer: installOrderStates() - updates/creates all states via ObjectModel
 * 2. SQL layer: directly enforces send_email and template as authoritative fallback.
 *    Resolves state IDs via Configuration::get() first; falls back to module_name lookup
 *    matched by color when config keys are missing.
 *
 * This script is idempotent and safe to run multiple times.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2025 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_1($module)
{
    // Register the new hook introduced in 1.2.1.
    // install() does not run on upgrade, so hooks added to it must be registered here explicitly.
    // Hook::registerHook() uses Shop::getCompleteListOfShopsID() when no shop list is passed,
    // which reliably covers all shops in all contexts including upgrade.
    $module->registerHook('actionGetExtraMailTemplateVars');

    // Primary path: let the module reinstall states via ORM.
    // This handles all languages, all fields, and is the canonical install path.
    // If config keys are missing, it searches by name and re-saves the IDs.
    $module->installOrderStates();

    // SQL fallback: directly enforce send_email and template values.
    // Runs unconditionally so even a silent ORM failure is corrected.
    // Style note: the entire module uses raw Db::execute() with pSQL() for
    // upgrade-layer writes. Keep it consistent here instead of mixing in
    // Db::update() so a single mental model covers all upgrade-*.php files.
    $db = Db::getInstance();
    $success = true;

    // Color is unique per state and reliable even when config keys are lost.
    // send_email is intentionally 0 for *all* states - see file docblock.
    $fixes = [
        // [ configKey,                        color,      send_email, template ]
        ['ORDERWITHDRAWL_STATE_ID',          '#FFA500',  0, ''],
        ['ORDERWITHDRAWL_STATE_APPROVED_ID', '#28A745',  0, 'withdrawl_approved'],
        ['ORDERWITHDRAWL_STATE_REJECTED_ID', '#DC3545',  0, 'withdrawl_rejected'],
    ];

    foreach ($fixes as $fix) {
        list($configKey, $color, $sendEmail, $template) = $fix;

        // Primary: resolve via config key (set during install or by installOrderStates() above)
        $stateId = (int) Configuration::get($configKey);

        // Fallback: locate by module_name + color when config key is missing
        if ($stateId <= 0) {
            $row = $db->getRow(
                'SELECT `id_order_state` FROM `' . _DB_PREFIX_ . 'order_state`
                 WHERE `module_name` = \'' . pSQL($module->name) . '\'
                   AND `color` = \'' . pSQL($color) . '\''
            );
            if ($row) {
                $stateId = (int) $row['id_order_state'];
                // Persist the recovered ID so future calls don't need the fallback
                Configuration::updateValue($configKey, $stateId);
            }
        }

        if ($stateId <= 0) {
            // State not found via config key or module_name+color.
            // installOrderStates() above will have created it fresh with the correct values.
            continue;
        }

        $success = $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_state`
             SET `send_email` = ' . (int) $sendEmail . '
             WHERE `id_order_state` = ' . $stateId
        ) && $success;

        $success = $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_state_lang`
             SET `template` = \'' . pSQL($template) . '\'
             WHERE `id_order_state` = ' . $stateId
        ) && $success;
    }

    return $success;
}
