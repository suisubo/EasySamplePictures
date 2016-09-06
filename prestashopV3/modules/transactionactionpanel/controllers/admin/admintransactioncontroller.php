<?php

if (!defined('_PS_VERSION_'))
	exit;

class AdminTransactionController extends AdminController
{
	public function __construct()
	{	
		parent::__construct();
	}
	
	public function renderForm()
	{
		if(!$this->ajax)
		{
			$moduleInstance = Module::getInstanceByName('transactionactionpanel');
			return $moduleInstance->displayAllTransactions();
		}else{
			return '';
		}
	}
	
	public function initProcess()
	{
	 	$this->display = 'edit';
	}
	
	public function displayAjax()
	{
		$moduleInstance = Module::getInstanceByName ( 'transactionactionpanel' );
		$this->ajaxDie (Tools::jsonEncode($moduleInstance->processActions()));	
	}
	
}