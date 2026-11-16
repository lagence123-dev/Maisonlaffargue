<?php
namespace MoneticoPaiement\Model;

class MoneticoPaiementRefund extends \ObjectModel
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $order_id;
    /**
     * @var float
     */
    public $amount;
    /**
     * @var string
     */
    public $refund_context;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'monetico_refund',
        'primary' => 'id',
        'fields' => [
            'order_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ],
            'amount' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat',
                'required' => true
            ],
            'refund_context' => [
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
    public function getOrderId(): int
    {
        return $this->order_id;
    }

    /**
     * @param int $order_id
     */
    public function setOrderId(int $order_id)
    {
        $this->order_id = $order_id;
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
    public function getRefundContext(): string
    {
        return $this->refund_context;
    }

    /**
     * @param string $refund_context
     */
    public function setRefundContext(string $refund_context)
    {
        $this->refund_context = $refund_context;
    }

}