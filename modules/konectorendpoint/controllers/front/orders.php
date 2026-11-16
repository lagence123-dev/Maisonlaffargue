<?php

class KonnctorEndpointOrdersModuleFrontControlle extends ModuleFrontController
{
    /** @var ContextCore context */
    private $context;

    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();
    }

    /*
	 * main controller action
	 */
    public function initContent()
    {
        parent::initContent();

        $response = [
            'status' => 'ok',
            'message' => 'yolo'
        ];

        // Classic json response
        $json = json_encode($response);
        $this->ajaxRender($json);

    }


}
