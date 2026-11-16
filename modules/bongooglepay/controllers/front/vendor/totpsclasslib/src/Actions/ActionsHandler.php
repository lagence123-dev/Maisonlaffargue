<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
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
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @version   release/2.1.0
 */
namespace Stripe_officialClasslib\Actions;

/**
 * Actions Handler
 */
class ActionsHandler
{
    /**
     * @var ObjectModel
     */
    protected $modelObject;

    /**
     * Values conveyored by the classes
     *
     * @var array
     */
    protected $conveyor = [];

    /**
     * List of actions
     *
     * @var array
     */
    protected $actions;

    /**
     * Set an modelObject
     *
     * @param ObjectModel $modelObject
     *
     * @return $this
     */
    public function setModelObject($modelObject)
    {
        $this->modelObject = $modelObject;

        return $this;
    }

    /**
     * Set the conveyor
     *
     * @param array $conveyorData
     *
     * @return $this
     */
    public function setConveyor($conveyorData)
    {
        $this->conveyor = $conveyorData;

        return $this;
    }

    /**
     * Return data in conveyor
     *
     * @return array
     */
    public function getConveyor()
    {
        return $this->conveyor;
    }

    /**
     * Call sevral actions
     *
     * @param mixed $actions
     *
     * @return $this
     */
    public function addActions($actions)
    {
        $this->actions = func_get_args();

        return $this;
    }

    /**
     * Process the action call back of cross modules
     *
     * @param string $chain Name of the actions chain
     *
     * @return bool
     */
    public function process($chain)
    {
        $className = \Tools::ucfirst($chain) . 'Actions';
        if (!preg_match('/^[a-zA-Z]+$/', $className)) {
            throw new \Exception($className . '" class name not valid "');
        }
        include_once _PS_MODULE_DIR_ . 'stripe_official/classes/actions/' . $className . '.php';

        $overridePath = _PS_OVERRIDE_DIR_ . 'modules/stripe_official/classes/actions/' . $className . '.php';
        if (file_exists($overridePath)) {
            $className .= 'Override';
            include_once $overridePath;
        }

        if (class_exists($className)) {
            /** @var Stripe_officialDefaultActions $classAction */
            $classAction = new $className();
            $classAction->setModelObject($this->modelObject);
            $classAction->setConveyor($this->conveyor);

            foreach ($this->actions as $action) {
                if (!is_callable([$classAction, $action], false, $callable_name)) {
                    continue;
                }
                if (!call_user_func_array([$classAction, $action], [])) {
                    $this->setConveyor($classAction->getConveyor());

                    return false;
                }
            }

            $this->setConveyor($classAction->getConveyor());
        } else {
            throw new \Exception($className . '" class not defined "');
        }

        return true;
    }
}
