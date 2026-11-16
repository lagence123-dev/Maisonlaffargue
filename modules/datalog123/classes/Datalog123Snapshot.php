<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/Datalog123Inventory.php';

/** The single snapshot is served dynamically when Webservice loads id 1. */
class Datalog123Snapshot extends ObjectModel
{
    public $snapshot;
    public $generated_at;

    public static $definition = array(
        'table' => 'datalog123_snapshot',
        'primary' => 'id_datalog123_snapshot',
        'fields' => array(
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'datalog123',
        'objectsNodeName' => 'datalog123',
        'fields' => array(
            'snapshot' => array('getter' => 'getSnapshotForWebservice'),
            'generated_at' => array('getter' => 'getGeneratedAtForWebservice'),
            'created_at' => array('getter' => 'getCreatedAtForWebservice'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null, Context $context = null)
    {
        // Synthetic read-only singleton. No DB row required to fetch /api/datalog123/1.
        if ((int) $id === 1) {
            $this->id = 1;
            $this->id_datalog123_snapshot = 1;
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            parent::__construct($id, $id_lang, $id_shop, $context);
        }
    }

    public function getSnapshotForWebservice()
    {
        dump('arezfd');die;
        return json_encode(Datalog123Inventory::collect(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function getGeneratedAtForWebservice()
    {
        return gmdate('c');
    }

    public function getCreatedAtForWebservice()
    {
        return gmdate('Y-m-d H:i:s');
    }
}
