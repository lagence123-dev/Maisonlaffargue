<?php

namespace MoneticoPaiement\Model;

use Db;
use MoneticoPaiement\Model\MoneticoPaiementCodeSiteOptions;
use MoneticoPaiement\Model\MoneticoPaiementHelper;

class MoneticoPaiementCodeSite extends \ObjectModel
{

    /**
     * @var int
     */
    public $id_code_site;

    /**
     * @var string
     */
    public $code_site_display_name;

    /**
     * @var int
     */
    public $code_site_ept_id;
    /**
     * @var string
     */
    public $code_site_name;
    /**
     * @var string
     */
    public $code_site_payment_method;
    /**
     * @var string
     */
    public $code_site_langue;
    /**
     * @var boolean
     */
    public $code_site_active;
    /**
     * @var boolean
     */
    public $code_site_sandbox;
    /**
     * @var array
     */
    public $opts;
    /**
     * @var array
     */
    public $_3dsv2;
    /**
     * @var boolean
     */
    public $code_site_express_payment;
    /**
     * @var boolean
     */
    public $code_site_amount_option;
    /**
     * @var float|null
     */
    public $code_site_amount_min;
    /**
     * @var float|null
     */
    public $code_site_amount_max;


    public static $definition = [
        'table' => 'monetico_code_site',
        'primary' => 'id_code_site',
        'fields' => [
            'code_site_display_name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'code_site_name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => '255',
                'required' => true
            ],
            'code_site_payment_method' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => '64',
                'required' => true
            ],
            'code_site_ept_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'code_site_langue' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'code_site_active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ],
            'code_site_sandbox' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ],
            'code_site_express_payment' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ],
            'code_site_amount_min' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => false,
                'allow_null' => true
            ],
            'code_site_amount_max' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => false,
                'allow_null' => true
            ],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        // call parent for instantiate the object
        Parent::__construct($id, $id_lang, $id_shop, $translator);
        // add Code Site Options
        $this->__constructOptions();
    }

    public function __constructOptions()
    {

        if ($this->getIdCodeSite()) {

            // options
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from('monetico_code_site_opts');
            $sql->where('code_site_id = ' . (int)$this->getIdCodeSite());
            $sql_opts = Db::getInstance()->executeS($sql);

            $array_options = [];
            foreach ($sql_opts as $opt) {
                // for the names of the options that end with "s", return an array
                $multiple_option = (mb_substr($opt['opt'], -1) === 's');
                if ($multiple_option) {
                    $array_options[$opt['opt']][] = $opt;
                } else {
                    $array_options[$opt['opt']] = $opt;
                }
            }

            $this->setOpts($array_options);

            // 3dsv2
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from('monetico_code_site_3dsv2');
            $sql->where('code_site_id = ' . (int)$this->getIdCodeSite());

            $this->set3DSV2(Db::getInstance()->executeS($sql));
        }
    }

    /**
     * @return int|null
     */
    public function getIdCodeSite()
    {
        return $this->id_code_site;
    }

    /**
     * @param int $id_code_site
     */
    public function setIdCodeSite(int $id_code_site)
    {
        $this->id_code_site = $id_code_site;
    }

    /**
     * @return string|null
     */
    public function getCodeSiteDisplayName()
    {
        return $this->code_site_display_name;
    }

    /**
     * @param string $code_site_display_name
     */
    public function setCodeSiteDisplayName(string $code_site_display_name)
    {
        $this->code_site_display_name = $code_site_display_name;
    }

    /**
     * @return int|null
     */
    public function getCodeSiteEptId()
    {
        return $this->code_site_ept_id;
    }

    /**
     * @param int $code_site_ept_id
     */
    public function setCodeSiteEptId(int $code_site_ept_id)
    {
        $this->code_site_ept_id = $code_site_ept_id;
    }

    /**
     * @return string|null
     */
    public function getCodeSiteName()
    {
        return $this->code_site_name;
    }

    /**
     * @param string $code_site_name
     */
    public function setCodeSiteName(string $code_site_name)
    {
        $this->code_site_name = $code_site_name;
    }

    /**
     * @return string
     */
    public function getCodeSitePaymentMethod(): string
    {
        return $this->code_site_payment_method;
    }

    /**
     * @param string $code_site_payment_method
     */
    public function setCodeSitePaymentMethod(string $code_site_payment_method): void
    {
        $this->code_site_payment_method = $code_site_payment_method;
    }


    /**
     * @return string|null
     */
    public function getCodeSiteLangue()
    {
        return $this->code_site_langue;
    }

    /**
     * @param string $code_site_langue
     */
    public function setCodeSiteLangue(string $code_site_langue)
    {
        $this->code_site_langue = $code_site_langue;
    }

    /**
     * @return bool|null
     */
    public function isCodeSiteActive()
    {
        return $this->code_site_active;
    }

    /**
     * @param bool $code_site_active
     */
    public function setCodeSiteActive(bool $code_site_active)
    {
        $this->code_site_active = $code_site_active;
    }

    /**
     * @return bool|null
     */
    public function isCodeSiteSandbox()
    {
        return $this->code_site_sandbox;
    }

    /**
     * @param bool $code_site_sandbox
     */
    public function setCodeSiteSandbox(bool $code_site_sandbox)
    {
        $this->code_site_sandbox = $code_site_sandbox;
    }

    /**
     * @return mixed
     */
    public function getOpts()
    {
        return $this->opts;
    }

    /**
     * @param mixed $opts
     */
    public function setOpts($opts)
    {
        $this->opts = $opts;
    }

    /**
     * @param mixed $opts
     */
    public function set3DSV2($_3dsv2)
    {
        $this->_3dsv2 = $_3dsv2;
    }

    /**
     * @return bool
     */
    public function isCodeSiteExpressPayment(): bool
    {
        return $this->code_site_express_payment;
    }

    /**
     * @param bool $code_site_express_payment
     */
    public function setCodesiteExpressPayment(bool $code_site_express_payment)
    {
        $this->code_site_express_payment = $code_site_express_payment;
    }

    /**
     * @return bool
     */
    public function isCodeSiteAmountOption(): bool
    {
        return (bool)$this->code_site_amount_option;
    }

    /**
     * @param bool $code_site_amount_option
     */
    public function setCodeSiteAmountOption(bool $code_site_amount_option): void
    {
        $this->code_site_amount_option = $code_site_amount_option;
    }

    /**
     * @return float|null
     */
    public function getCodeSiteAmountMin()
    {
        return $this->code_site_amount_min;
    }

    /**
     * @param float|null $code_site_amount_min
     */
    public function setCodeSiteAmountMin(?float $code_site_amount_min): void
    {
        $this->code_site_amount_min = $code_site_amount_min;
    }


    /**
     * @return float|null
     */
    public function getCodeSiteAmountMax()
    {
        return $this->code_site_amount_max;
    }

    /**
     * @param float|null $code_site_amount_max
     */
    public function setCodeSiteAmountMax(?float $code_site_amount_max): void
    {
        $this->code_site_amount_max = $code_site_amount_max;
    }


    /**
     * get the object to an array
     * @return array
     */
    public function toArray()
    {
        $data_options = [];
        if (is_array($this->getOpts()) && count($this->getOpts()) > 0) {
            foreach ($this->getOpts() as $key => $option) {
                $data_options[$key] = $option;
            }
        }

        return array_merge([
            'id_code_site' => $this->getIdCodeSite(),
            'code_site_display_name' => $this->getCodeSiteDisplayName(),
            'code_site_name' => $this->getCodeSiteName(),
            'code_site_payment_method' => $this->getCodeSitePaymentMethod(),
            'code_site_ept_id' => $this->getCodeSiteEptId(),
            'code_site_langue' => $this->getCodeSiteLangue(),
            'code_site_active' => $this->isCodeSiteActive(),
            'code_site_sandbox' => $this->isCodeSiteSandbox(),
            'code_site_express_payment' => $this->isCodeSiteExpressPayment(),
            'code_site_amount_option' => $this->isCodeSiteAmountOption(),
            'code_site_amount_min' => $this->getCodeSiteAmountMin(),
            'code_site_amount_max' => $this->getCodeSiteAmountMax(),
        ], $data_options, ['3dsv2' => $this->_3dsv2]);
    }

    /**
     * get option from getOpts()
     *
     * @param $name
     *
     * @return array|mixed
     */
    public function getOption($name)
    {
        // for the names of the options that end with "s", return an array
        $multiple_option = (mb_substr($name, -1) === 's');

        if ($multiple_option) {
            $data_option = [];
        } else {
            $data_option = null;
        }

        if (is_array($this->getOpts()) && count($this->getOpts()) > 0) {
            if (isset($this->getOpts()[$name])) {
                if ($multiple_option) {
                    foreach ($this->getOpts()[$name] as $option) {
                        $data_option[] = $option['value'];
                    }
                } else {
                    $data_option = $this->getOpts()[$name]['value'];
                }
            }
        }
        return $data_option;
    }

    /**
     * get specific option from db
     * @return array|string|null
     * @var $name
     */
    public function getOptionFromDb($name)
    {
        // for the names of the options that end with "s", return an array
        $multiple_option = (mb_substr($name, -1) === 's');

        if ($multiple_option) {
            $data_option = [];
        } else {
            $data_option = null;
        }

        if ($this->getIdCodeSite()) {

            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from('monetico_code_site_opts');
            $sql->where('code_site_id = ' . (int)$this->getIdCodeSite());
            $sql->where('opt = "' . pSQL($name) . '"');

            $options = Db::getInstance()->executeS($sql);
            if ($multiple_option) {
                foreach ($options as $option) {
                    $data_option[] = $option['value'];
                }
            } else if (count($options) > 0) {
                $data_option = array_shift($options)['value'];
            }
        }
        return $data_option;
    }

    /**
     * Return the 3dsv2 constraints
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public function get3DSV2(): array
    {
        $data_3dsv2 = [];

        if ($this->getIdCodeSite()) {
            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from('monetico_code_site_3dsv2');
            $sql->where('code_site_id = ' . (int)$this->getIdCodeSite());

            $data_3dsv2 = Db::getInstance()->executeS($sql);
        }

        return $data_3dsv2;
    }

    /**
     * Update code site options
     *
     * @param $name
     * @param $options
     *
     * @return bool
     * @throws \PrestaShopException
     */
    public function updateOptions($name, $options)
    {
        $return = true;
        // for the names of the options that end with "s", return an array
        $multiple_option = (mb_substr($name, -1) === 's');
        $bdd_options = $this->getOptionFromDb($name);
        $to_delete = [];
        $to_add = [];
        // created options
        if (is_null($bdd_options)) {
            if (is_array($options)) {
                $to_add = $options;
            } else {
                $to_add[] = $options;
            }

            foreach ($to_add as $new_value) {

                $opt = new MoneticoPaiementCodeSiteOptions();
                $opt->setCodeSiteId($this->getIdCodeSite());
                $opt->setOpt($name);
                $opt->setValue($new_value);
                $return &= $opt->save();
            }
        } // updated options, it's an array
        else if (is_array($options)) {
            $to_add = array_diff($options, $bdd_options);
            $to_delete = array_diff($bdd_options, $options);

            if (count($to_add)) {
                foreach ($to_add as $new_value) {
                    $opt = new MoneticoPaiementCodeSiteOptions();
                    $opt->setCodeSiteId($this->getIdCodeSite());
                    $opt->setOpt($name);
                    $opt->setValue($new_value);
                    $return &= $opt->save();
                }
            }
        } // not an array , updatea an option
        else if (!$multiple_option) {

            $sql = new \DbQuery();
            $sql->select('*');
            $sql->from('monetico_code_site_opts');
            $sql->where('code_site_id = ' . (int)$this->getIdCodeSite());
            $sql->where('opt = "' . pSQL($name) . '"');

            $option = Db::getInstance()->executeS($sql);

            $opt = new MoneticoPaiementCodeSiteOptions(array_shift($option)['id']);
            $opt->setValue($options);
            $return &= $opt->save();

        } else if (count($bdd_options) > 0) {
            $to_delete = $bdd_options;
        }


        $nb_to_delete = count($to_delete);
        if ($nb_to_delete > 0) {
            $to_delete = array_map(function ($to_delete) {
                return '"' . pSQL($to_delete) . '"';
            }, $to_delete);
            $to_delete_in_string = implode(',', $to_delete);
            $request = '`code_site_id` = ' . (int)$this->getIdCodeSite() . ' and `opt` = "' . pSQL($name) . '" and `value` IN (' . $to_delete_in_string . ')';
            $return &= Db::getInstance()->delete('monetico_code_site_opts', $request);
        }

        return $return;
    }

    /**
     * Update code site 3dsv2
     *
     * @return bool
     * @throws \PrestaShopException
     */
    public function update3dsv2()
    {
        $return = true;
        $previous_3dsv2 = $this->get3DSV2();

        if (empty($previous_3dsv2)) {
            foreach ($this->_3dsv2 as $key => $stat) {
                if (isset($stat['amount']) && ($stat['amount'] > 0) && isset($stat['state']) || $key === 1) {
                    $mp_code_site_3dsv2 = new MoneticoPaiementCodeSite3dsv2();
                    $mp_code_site_3dsv2->setCodeSiteId($this->getIdCodeSite());
                    $mp_code_site_3dsv2->setAmount($key === 1 ? 0 : $stat['amount']);
                    $mp_code_site_3dsv2->setState($stat['state']);
                    $return &= $mp_code_site_3dsv2->save();
                }
            }
        } else {

            foreach ($this->_3dsv2 as $key => $stat) {
                if (isset($stat['amount']) && ($stat['amount'] > 0) && isset($stat['state']) || $key === 1) {
                    $constraint = array_shift($previous_3dsv2);
                    $mp_code_site_3dsv2 = new MoneticoPaiementCodeSite3dsv2($constraint['id'] ?? 0);
                    $mp_code_site_3dsv2->setCodeSiteId($this->getIdCodeSite());
                    $mp_code_site_3dsv2->setAmount($key === 1 ? 0 : $stat['amount']);
                    $mp_code_site_3dsv2->setState($stat['state']);
                    $return &= $mp_code_site_3dsv2->save();
                }
            }

            if (count($previous_3dsv2) > 0) {
                foreach ($previous_3dsv2 as $to_delete) {
                    $mp_code_site_3dsv2 = new MoneticoPaiementCodeSite3dsv2($to_delete['id']);
                    $return &= $mp_code_site_3dsv2->delete();
                }
            }
        }

        return $return;
    }

    /**
     * Save the Code Site (override)
     *
     * @param false $null_values
     * @param bool  $auto_date
     *
     * @return bool
     * @throws \PrestaShopException
     */
    public function save($null_values = false, $auto_date = true)
    {
        $return = Parent::save($null_values, $auto_date);
        if ($return) {
            // we assign last id if it's new
            if (!$this->id_code_site) {
                $this->id_code_site = MoneticoPaiementHelper::getLastCodeSiteId();
            }
            // update the 3dsv2 values
            $return &= $this->update3dsv2();
            // update the option values
            foreach ($this->getOpts() as $key => $new_values) {
                $return &= $this->updateOptions($key, $new_values);
            }
        }
        return $return;
    }


    /**
     * Delete Code Site (override)
     * @return bool
     * @throws \PrestaShopException
     */
    public function delete()
    {
        // delete options and remove code site himself
        $request = '`code_site_id` = ' . (int)$this->getIdCodeSite();
        return Db::getInstance()->delete('monetico_code_site_opts', $request) && Db::getInstance()->delete('monetico_code_site_3dsv2', $request) && Parent::delete();
    }

}