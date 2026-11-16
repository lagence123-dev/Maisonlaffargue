<?php

namespace MoneticoPaiement\Model;

class MoneticoPaiementCodeSiteOptions extends \ObjectModel
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
     * @var string
     */
    public $opt;

    /**
     * @var string
     */
    public $value;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'monetico_code_site_opts',
        'primary' => 'id',
        'fields' => [
            'code_site_id' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'opt' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'value' => [
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
     * @return string
     */
    public function getOpt(): string
    {
        return $this->opt;
    }

    /**
     * @param string $opt
     */
    public function setOpt(string $opt)
    {
        $this->opt = $opt;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}