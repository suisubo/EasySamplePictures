<?php
if (!defined('_PS_VERSION_'))
	exit;

class transactionactionpanel extends Module
{
	public function __construct()
	{
		$this->name = 'transactionactionpanel';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Super Innovation LLC';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;
	
		parent::__construct();
	
		$this->displayName = $this->l('Transaction Action Panel');
		$this->description = $this->l('Display Action List in Order List.');	
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	}
	
	public function install()
	{
		$success = (parent::install()
			&& $this->registerHook('displayOrderDetail')
		);

		$this->_clearCache('*');

		return $success;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
			return true;
	}
}	