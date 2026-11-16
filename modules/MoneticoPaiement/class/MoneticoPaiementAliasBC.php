<?php

namespace MoneticoPaiement\Model;

class MoneticoPaiementAliasBC extends \ObjectModel
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
     * @var int
     */
    public $user_id;
    /**
     * @var string
     */
    public $alias;
    /**
     * @var boolean
     */
    public $certified;

    public static $definition = [
        'table' => 'monetico_alias_bc',
        'primary' => 'id',
        'fields' => [
            'id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
            ],
            'code_site_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'user_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'alias' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'certified' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
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
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return bool
     */
    public function isCertified(): bool
    {
        return $this->certified;
    }

    /**
     * @param bool $certified
     */
    public function setCertified(bool $certified)
    {
        $this->certified = $certified;
    }

}