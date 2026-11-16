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

use Translate;

/**
 * DefaultActions
 */
class DefaultActions
{
    /**
     * @var \ObjectModel
     */
    protected $modelObject;

    /**
     * Values conveyored by the classes
     *
     * @var array
     */
    protected $conveyor = [];

    /**
     * Set the modelObject
     *
     * @param \ObjectModel $modelObject
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
     * Call next action call back of cross modules
     *
     * @param mixed $action Name of the actions chain
     *
     * @return bool
     */
    protected function forward($action)
    {
        if (!is_callable([$this, $action], false)) {
            echo $action . ' not defined';
            exit;
        }
        if (!call_user_func_array([$this, $action], [])) {
            return false;
        }

        return true;
    }

    /**
     * Translation function; needed so PS will properly parse the file
     *
     * @param string $string the string to translate
     * @param string $source the file with the translation; should always be the current file
     *
     * @return mixed|string
     */
    protected function l($string, $source)
    {
        return \Translate::getModuleTranslation('stripe_official', $string, $source);
    }
}
