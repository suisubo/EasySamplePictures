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
			&& $this->registerHook('header')
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
	
	public function hookHeader($params)
	{
		$this->context->controller->addJS($this->_path.'views/js/transactionactionpanel.js');
	}
	
	public function hookdisplayOrderDetail($params)
	{
		$db = Db::getInstance();
		
		$order = $params["order"];
		$id_order = $order->id;
		
		$sql = 'select * from '._DB_PREFIX_.'z_transaction where id_order = '.$id_order;
		
		$transactions = $db->ExecuteS($sql);
		
		if(!$transactions)
			return '';
		
		foreach ($transactions as $transaction)		
		{
			$id_product = $transaction["id_product"];
			$sql = 'select * from '._DB_PREFIX_.'z_service_product where id_product = '.$id_product;
			
			$service_products = $db->ExecuteS($sql);
			
			if(!$service_products)
				continue;
			
			foreach ($service_products as $servie_product)
			{
				$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '
						.$servie_product['id_service_type'].' AND id_step = '.$transaction['current_step'];
							
				$service_steps = $db->ExecuteS($sql);
				
				if(!$service_steps)
					continue;
				
				foreach ($service_steps as $service_step)
				{
					$id_step_type = $service_step["id_step_type"];
					$action_partner = $service_step["action_partner"];
					$instruction = $service_step["instruction"];
					$description = $service_step["description"];
		
					if($action_partner == 0)
					{
						$sql = 'select * from '._DB_PREFIX_.'z_step_type_ui where id_step_type = '.$id_step_type.' order by sequence';
							
						$step_ui = $db->ExecuteS($sql);
						
						$transactions_ui[] = array("id_transaction" => $transaction['id_transaction'],
								"current_step" => $transaction['current_step'],
								"instruction" => $instruction,
								"description" => $description,
								"ui_list" => $step_ui);
					}
		
				}
			}			
		}
		
		if(isset($transactions_ui))
		{
			$this->smarty->assign(array(
					'transactions' => $transactions_ui
			));			
			
			return $this->display(__FILE__, 'transactionactionpanel.tpl', $this->getCacheId('transactionactionpanel.tpl'));
		}else {
			return '';
		}		
	}
}	