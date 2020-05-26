<?php

use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

class Goprediction extends Module
{
    public function __construct()
    {
        $this->name = 'goprediction';
        $this->author = 'Marek Koncewicz';
        $this->version = '2.0.1';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('GoPrediction', array(), 'Modules.Goprediction.Admin');
        $this->description = $this->trans('Adds an input field to provide an URL to a HTML/CSS/JS creation.', array(), 'Modules.Goprediction.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.4.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue('GOPREDICTION_CREATION_URL', '')
            && $this->registerHook('actionMaintenancePageForm')
            && $this->registerHook('actionAdminMaintenanceControllerPostProcessBefore')
            && $this->registerHook('header')
        ;
    }

    public function hookHeader()
    {
        $url = Configuration::get('GOPREDICTION_CREATION_URL');

        if ($url) {
            $this->context->controller->registerJavascript(
                'go-prediction',
                $url, [
                'server' => 'remote',
                'position' => 'bottom',
                'priority' => 2000
            ]);
        }
    }

    public function hookActionAdminMaintenanceControllerPostProcessBefore($data)
    {
        $url = $data['request']->request->get('form')['general']['goprediction_url'];
        Configuration::updateValue('GOPREDICTION_CREATION_URL', $url);
    }

    public function hookActionMaintenancePageForm(&$hookParams)
    {
        $formBuilder = $hookParams['form_builder'];
        $uploadQuotaForm = $formBuilder->get('general');
        $uploadQuotaForm->add(
            'goprediction_url',
            TextType::class,
            [
                'required' => false,
                'data' => Configuration::get('GOPREDICTION_CREATION_URL'),
                'label' => 'GoPrediction URL'
            ]
        );
    }
}
