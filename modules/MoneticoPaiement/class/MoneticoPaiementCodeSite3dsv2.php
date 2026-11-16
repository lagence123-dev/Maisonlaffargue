<?php
namespace MoneticoPaiement\Model;

class MoneticoPaiementCodeSite3dsv2 extends \ObjectModel
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $code_site_id;
    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $state;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'monetico_code_site_3dsv2',
        'primary' => 'id',
        'fields' => [
            'code_site_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'amount' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true
            ],
            'state' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
        ],
    ];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getCodeSiteId(): int
    {
        return $this->code_site_id;
    }

    /**
     * @param int $code_site_id
     */
    public function setCodeSiteId(int $code_site_id)
    {
        $this->code_site_id = $code_site_id;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state)
    {
        $this->state = $state;
    }


}