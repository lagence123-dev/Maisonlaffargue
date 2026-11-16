<?php
if (!defined('_PS_VERSION_')) { exit; }

class Datalog123 extends Module
{
    public function __construct()
    {
        $this->name = 'datalog123';
        $this->tab = 'administration';
        $this->version = '2.0.0';
        $this->author = 'LAGENCE123';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.7.7.0', 'max' => '9.99.99');
        parent::__construct();
        $this->displayName = 'Datalog123';
        $this->description = 'API de diagnostic technique en lecture seule pour extranet LAGENCE123.';
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue('DATALOG123_MODULES_SOURCE', 'disk')
            && $this->registerHook('addWebserviceResources');
    }

    public function hookAddWebserviceResources($params)
    {
        require_once __DIR__ . '/classes/WebserviceSpecificManagementDatalog123.php';
        return array(
            'datalog123' => array(
                'description' => 'Datalog123 - diagnostic technique (lecture seule)',
                'specific_management' => true,
                'forbidden_method' => array('POST', 'PUT', 'PATCH', 'DELETE'),
            ),
        );
    }

    public function uninstall()
    {
        return Configuration::deleteByName('DATALOG123_MODULES_SOURCE') && parent::uninstall();
    }

    public function getContent()
    {
        $message = '';
        if (Tools::isSubmit('submitDatalog123Settings')) {
            $source = (string) Tools::getValue('DATALOG123_MODULES_SOURCE');
            if (!in_array($source, array('disk', 'database'), true)) {
                $message = $this->displayError($this->l('Methode de recuperation invalide.'));
            } elseif (Configuration::updateValue('DATALOG123_MODULES_SOURCE', $source)) {
                $message = $this->displayConfirmation($this->l('Configuration enregistree.'));
            } else {
                $message = $this->displayError($this->l('Impossible d enregistrer la configuration.'));
            }
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = 0;
        $helper->submit_action = 'submitDatalog123Settings';
        $helper->fields_value = array(
            'DATALOG123_MODULES_SOURCE' => Configuration::get('DATALOG123_MODULES_SOURCE') === 'database' ? 'database' : 'disk',
        );

        $form = array(
            'form' => array(
                'legend' => array('title' => $this->l('Configuration Datalog123'), 'icon' => 'icon-cogs'),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Methode de recuperation des modules'),
                        'name' => 'DATALOG123_MODULES_SOURCE',
                        'required' => true,
                        'values' => array(
                            array('id' => 'source_disk', 'value' => 'disk', 'label' => $this->l('Disque (par defaut) : modules presents sur le serveur')),
                            array('id' => 'source_database', 'value' => 'database', 'label' => $this->l('Base de donnees : modules enregistres dans PrestaShop')),
                        ),
                        'desc' => $this->l('Le statut actif et les informations complementaires restent consultes en base ou sur disque selon les besoins.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Enregistrer')),
            ),
        );

        return $message
            . $helper->generateForm(array($form))
            . '<div class="panel"><h3>Datalog123</h3><p>Activez le Webservice natif PrestaShop, puis creez une cle autorisee uniquement en GET sur <strong>datalog123</strong>.</p><p>Endpoint : <code>/api/datalog123?output_format=JSON</code>.</p><p>Ne donnez jamais a cette cle de permissions sur d autres ressources.</p></div>';
    }
}