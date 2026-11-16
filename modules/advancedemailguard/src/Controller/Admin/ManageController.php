<?php
/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2026 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\AdvancedEmailGuard\Controller\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

class ManageController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction()
    {
        // I'm not proud of this, but this is just a temporary solution
        // until I (eventually) refactor the module to use Symfony's routing and controller system properly (and twig).
        require_once _PS_MODULE_DIR_ . 'advancedemailguard/controllers/admin/AdminAdvancedEmailGuard.php';

        $legacyAdminCtrl = new \AdminAdvancedEmailGuardController();
        $legacyAdminCtrl->init();
        return new Response($legacyAdminCtrl->renderContent());
    }
}
