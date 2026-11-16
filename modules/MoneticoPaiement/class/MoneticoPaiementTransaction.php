<?php
namespace MoneticoPaiement\Model;

class MoneticoPaiementTransaction  extends \ObjectModel
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $order_ref;
    /**
     * @var int
     */
    public $cart_id;
    /**
     * @var int
     */
    public $code_site_id;
    /**
     * @var string
     */
    public $MAC;
    /**
     * @var string
     */
    public $order_context;
    /**
     * @var string
     */
    public $payment_response_context;
    /**
     * @var boolean
     */
    public $sandbox_transaction;
    /**
     * @var int
     */
    public $alias_id;
    /**
     * @var boolean
     */
    public $has_taxes;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'monetico_transaction',
        'primary' => 'id',
        'fields' => [
            'order_ref' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'cart_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'code_site_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'MAC' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'order_context' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true
            ],
            'payment_response_context'=>[
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ],
            'sandbox_transaction'=>[
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ],
            'alias_id'=>[
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'has_taxes'=> [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ]
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
     * @return string
     */
    public function getOrderRef(): string
    {
        return $this->order_ref;
    }

    /**
     * @param string $order_ref
     */
    public function setOrderRef(string $order_ref)
    {
        $this->order_ref = $order_ref;
    }

    /**
     * @return int
     */
    public function getCartId(): int
    {
        return $this->cart_id;
    }

    /**
     * @param int $cart_id
     */
    public function setCartId(int $cart_id)
    {
        $this->cart_id = $cart_id;
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
    public function getMAC(): string
    {
        return $this->MAC;
    }

    /**
     * @param string $MAC
     */
    public function setMAC(string $MAC)
    {
        $this->MAC = $MAC;
    }

    /**
     * @return string
     */
    public function getOrderContext(): string
    {
        return $this->order_context;
    }

    /**
     * @param string $order_context
     */
    public function setOrderContext(string $order_context)
    {
        $this->order_context = $order_context;
    }

    /**
     * @return string
     */
    public function getPaymentResponseContext(): string
    {
        return $this->payment_response_context;
    }

    /**
     * @param string $payment_response_context
     */
    public function setPaymentResponseContext(string $payment_response_context)
    {
        $this->payment_response_context = $payment_response_context;
    }

    /**
     * @return bool
     */
    public function isSandboxTransaction(): bool
    {
        return $this->sandbox_transaction;
    }

    /**
     * @param bool $sandbox_transaction
     */
    public function setSandboxTransaction(bool $sandbox_transaction)
    {
        $this->sandbox_transaction = $sandbox_transaction;
    }

    /**
     * @return int
     */
    public function getAliasId(): int
    {
        return $this->alias_id;
    }

    /**
     * @param int $alias_id
     */
    public function setAliasId(int $alias_id)
    {
        $this->alias_id = $alias_id;
    }

    /**
     * @return bool
     */
    public function hasTaxes(): bool
    {
        return $this->has_taxes ?? true;
    }

    /**
     * @param bool $has_taxes
     * @return void
     */
    public function setHasTaxes(bool $has_taxes): void
    {
        $this->has_taxes = $has_taxes;
    }
}