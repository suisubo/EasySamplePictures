<?php

if (!defined('_PS_VERSION_'))
	exit;
//TransactionActionPanelProcessActionModuleFrontController
class TransactionActionPanelProcessActionModuleFrontController extends ModuleFrontController
{
	public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}
	
	public function init()
	{
		parent::init();
		$this->display_column_left = false;
		$this->display_column_right = false;
	}
	
	public function setMedia()
	{
		parent::setMedia();
	}
	
	public function initContent()
	{
		parent::initContent();		
	}
	
	public function displayAjaxDemo()
	{
		$actionbutton = Tools::getValue('actionbutton');
		$current_step = Tools::getValue('current_step');
		
		if($actionbutton == 'transaction_nav_next')
			$current_step = $current_step + 1;
		else 
			$current_step = $current_step - 1;
		
		$params['id_step'] = $current_step;
		
		$params['id_transaction'] = Tools::getValue('transaction_id');
		
		$moduleInstance = Module::getInstanceByName('transactionactionpanel');
		$ajaxReturn['next_step'] = $moduleInstance->displayProductDemoPanel($params);
			
		$this->ajaxDie(Tools::jsonEncode($ajaxReturn));
	}
	
	public function displayAjax()
	{
		$moduleInstance = Module::getInstanceByName ( 'transactionactionpanel' );
		$this->ajaxDie (Tools::jsonEncode($moduleInstance->processActions()));		
	}	
}