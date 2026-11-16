<?php

namespace MoneticoPaiement\Model;

use Db;

class MoneticoPaiementEPT extends \ObjectModel
{

    /**
     * @var int
     */
    public $id_ept;

    /**
     * @var string
     */
    public $ept_number;
    /**
     * @var string
     */
    public $ept_mac;

    /**
     * @var string
     */
    public $ept_payment_mode;
    /**
     * @var bool
     */
    public $ept_instalment_default;
    /**
     * @var int
     */
    public $ept_instalment_term_nb;
    /**
     * @var int
     */
    public $ept_instalment_pct_1;
    /**
     * @var int
     */
    public $ept_instalment_pct_2;
    /**
     * @var int
     */
    public $ept_instalment_pct_3;
    /**
     * @var int
     */
    public $ept_instalment_pct_4;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'monetico_ept',
        'primary' => 'id_ept',
        'fields' => [
            'ept_number' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'ept_mac' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'ept_payment_mode' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'ept_instalment_default' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool'
            ],
            'ept_instalment_term_nb' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ],
            'ept_instalment_pct_1' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ],
            'ept_instalment_pct_2' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ],
            'ept_instalment_pct_3' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ],
            'ept_instalment_pct_4' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ],
        ],
    ];

    /**
     * @return int|null
     */
    public function getIdEpt()
    {
        return $this->id_ept;
    }

    /**
     * @param int $id_ept
     */
    public function setIdEpt(int $id_ept)
    {
        $this->id_ept = $id_ept;
    }

    /**
     * @return string|null
     */
    public function getEptNumber()
    {
        return $this->ept_number;
    }

    /**
     * @param string $ept_number
     */
    public function setEptNumber(string $ept_number)
    {
        $this->ept_number = $ept_number;
    }

    /**
     * @return string|null
     */
    public function getEptMac()
    {
        return $this->ept_mac;
    }

    /**
     * @param string $ept_mac
     */
    public function setEptMac(string $ept_mac)
    {
        $this->ept_mac = $ept_mac;
    }

    /**
     * @return string|null
     */
    public function getEptPaymentMode()
    {
        return $this->ept_payment_mode;
    }

    /**
     * @param string $ept_payment_mode
     */
    public function setEptPaymentMode(string $ept_payment_mode)
    {
        $this->ept_payment_mode = $ept_payment_mode;
    }

    /**
     * @return bool|null
     */
    public function isEptInstalmentDefault()
    {
        return $this->ept_instalment_default;
    }

    /**
     * @param bool $ept_instalment_default
     */
    public function setEptInstalmentDefault(bool $ept_instalment_default): void
    {
        $this->ept_instalment_default = $ept_instalment_default;
    }

    /**
     * @return int|null
     */
    public function getEptInstalmentTermNb()
    {
        return $this->ept_instalment_term_nb;
    }

    /**
     * @param int $ept_instalment_term_nb
     */
    public function setEptInstalmentTermNb(int $ept_instalment_term_nb): void
    {
        $this->ept_instalment_term_nb = $ept_instalment_term_nb;
    }


    /**
     * @return int
     */
    public function getEptInstalmentPct1()
    {
        return $this->ept_instalment_pct_1;
    }

    /**
     * @param int $ept_instalment_pct_1
     */
    public function setEptInstalmentPct1(int $ept_instalment_pct_1): void
    {
        $this->ept_instalment_pct_1 = $ept_instalment_pct_1;
    }

    /**
     * @return int
     */
    public function getEptInstalmentPct2()
    {
        return $this->ept_instalment_pct_2;
    }

    /**
     * @param int $ept_instalment_pct_2
     */
    public function setEptInstalmentPct2(int $ept_instalment_pct_2): void
    {
        $this->ept_instalment_pct_2 = $ept_instalment_pct_2;
    }

    /**
     * @return int
     */
    public function getEptInstalmentPct3()
    {
        return $this->ept_instalment_pct_3;
    }

    /**
     * @param int $ept_instalment_pct_3
     */
    public function setEptInstalmentPct3(int $ept_instalment_pct_3): void
    {
        $this->ept_instalment_pct_3 = $ept_instalment_pct_3;
    }

    /**
     * @return int
     */
    public function getEptInstalmentPct4()
    {
        return $this->ept_instalment_pct_4;
    }

    /**
     * @param int $ept_instalment_pct_4
     */
    public function setEptInstalmentPct4(int $ept_instalment_pct_4): void
    {
        $this->ept_instalment_pct_4 = $ept_instalment_pct_4;
    }

    /**
     * @param int $number
     *
     * @return int
     */
    public function getEptInstalmentPct(int $number)
    {
        switch ($number) {
            case 1:
                return $this->getEptInstalmentPct1();
            case 2:
                return $this->getEptInstalmentPct2();
            case 3:
                return $this->getEptInstalmentPct3();
            case 4:
                return $this->getEptInstalmentPct4();
            default:
                return 0;
        }
    }

    /**
     * Display the object to an array
     * @return array
     */
    public function toArray()
    {
        return [
            'id_ept' => $this->getIdEpt(),
            'ept_number' => $this->getEptNumber(),
            'ept_mac' => $this->getEptMac(),
            'ept_payment_mode' => $this->getEptPaymentMode(),
            'ept_instalment_default' => $this->isEptInstalmentDefault(),
            'ept_instalment_term_nb' => $this->getEptInstalmentTermNb(),
            'ept_instalment_pct_1' => $this->getEptInstalmentPct1(),
            'ept_instalment_pct_2' => $this->getEptInstalmentPct2(),
            'ept_instalment_pct_3' => $this->getEptInstalmentPct3(),
            'ept_instalment_pct_4' => $this->getEptInstalmentPct4(),
        ];
    }

    /**
     * Get all code site associate to one ept     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getEptCodeSite()
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('monetico_code_site');
        $sql->where('code_site_ept_id = ' . (int)$this->id_ept);
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Delete Ept (override)
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function delete()
    {
        // delete all code site associate to one ept
        $code_sites = $this->getEptCodeSite();
        $return = true;
        foreach ($code_sites as $code_site) {
            $cs = new MoneticoPaiementCodeSite($code_site['id_code_site']);
            $return &= $cs->delete();
        }

        return $return && parent::delete();
    }
}