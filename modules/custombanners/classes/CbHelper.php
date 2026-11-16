<?php
/**
 *  @author    Amazzing <modules@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CbHelper
{
    public $m = [];
    public $cb;
    public $db;
    public $local_path;

    public function __construct($cb)
    {
        $this->cb = $cb;
        $this->db = $cb->db;
        $this->local_path = _PS_MODULE_DIR_ . $cb->name . '/';
    }

    public function l($string)
    {
        return $this->cb->l($string, static::class);
    }

    public function sqlTable($suffix)
    {
        return $this->cb->sqlTable($suffix);
    }

    public function runSql($sql)
    {
        return $this->cb->runSql($sql);
    }
}
