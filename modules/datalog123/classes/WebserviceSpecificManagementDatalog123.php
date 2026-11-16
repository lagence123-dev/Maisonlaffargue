<?php
if (!defined('_PS_VERSION_')) { exit; }
require_once __DIR__ . '/Datalog123Collector.php';

class WebserviceSpecificManagementDatalog123 implements WebserviceSpecificManagementInterface
{
    protected $objOutput;
    protected $output = '';
    protected $wsObject;
    protected $urlSegment = array();

    public function setUrlSegment($segments) { $this->urlSegment = $segments; return $this; }
    public function getUrlSegment() { return $this->urlSegment; }
    public function getWsObject() { return $this->wsObject; }
    public function getObjectOutput() { return $this->objOutput; }
    public function setWsObject(WebserviceRequestCore $obj) { $this->wsObject = $obj; return $this; }
    public function setObjectOutput(WebserviceOutputBuilderCore $obj) { $this->objOutput = $obj; return $this; }
    public function getContent()
    {
        return $this->output;
    }
    public function manage()
    {
        // The native webservice validates the API key and GET permission before this method.
        // Always JSON to preserve existing extranet consumers (including without output_format).
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $this->output = '{"error":"Method not allowed"}';
            return;
        }
        try {
            $payload = Datalog123Collector::collect();
            $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
            $this->output = $json === false ? '{"error":"Encoding failed"}' : $json;
        } catch (Exception $e) {
            http_response_code(500);
            $this->output = '{"error":"Collection failed"}';
            PrestaShopLogger::addLog('Datalog123: ' . $e->getMessage(), 3);
        } catch (Throwable $e) {
            http_response_code(500);
            $this->output = '{"error":"Collection failed"}';
            PrestaShopLogger::addLog('Datalog123: ' . $e->getMessage(), 3);
        }
        // Existing specific management endpoints return raw JSON via overrideContent.
        if (!headers_sent()) { header('Content-Type: application/json; charset=utf-8'); }
    }
}
