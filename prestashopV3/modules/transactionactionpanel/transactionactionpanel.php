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
		$this->context->controller->addCSS($this->_path.'views/css/msform.css', 'all');
	}
	
	public function displayTransactionDetail($transaction)
	{
		$db = Db::getInstance();
		
		$id_product = $transaction["id_product"];
		$sql = 'select * from '._DB_PREFIX_.'z_service_product where id_product = '.$id_product;
			
		$service_products = $db->ExecuteS($sql);
		
		if(!$service_products)
			continue;
				
		//we should only have one product for each transaction, loop here is not really necessary, we use the service product to find out
		//the service type, together with step id we can find out step type
		foreach ($service_products as $servie_product)
		{
			$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '
					.$servie_product['id_service_type'].' AND id_step = '.$transaction['current_step'];
						
			$service_steps = $db->ExecuteS($sql);

			if(count($service_steps) == 0)
				continue;

			foreach ($service_steps as $service_step)
			{
				$id_step_type = $service_step["id_step_type"];
				$action_partner = $service_step["action_partner"];
				$instruction = $service_step["instruction"];
				$description = $service_step["description"];


				$sql = 'select * from '._DB_PREFIX_.'z_step_type where id_step_type = '.$id_step_type;
				$step_types = $db->ExecuteS($sql);
				
				$sql = 'select * from '._DB_PREFIX_.'z_transaction_context where id_transaction = "'.$transaction['id_transaction'].'"';
				$context_raw = $db->ExecuteS($sql);			
				
				foreach($context_raw as $context_row)
				{
					$param_name = $context_row['param_name'];
					$context_params[$param_name] = $context_row['param_value'];
				}
				
				$sql = 'select * from '._DB_PREFIX_.'z_service_step_mapping where id_step_type = '.$id_step_type
				.' and id_service_type = '.$servie_product['id_service_type'].' and id_step = '.$transaction['current_step'].' and direction = 0';
				
				$mappings = $db->ExecuteS($sql);
				foreach($mappings as $mapping)
				{
					if(!$mapping['regex'])
					{
						$context_param_value = $context_params[$mapping['context_para_name']];
						if($context_param_value != null)
						{
							$local_params[$mapping['local_para_name']] = $context_param_value;
						}
					}else{					
						foreach($context_params as $key => $value)
						{
							if(preg_match('/'.$mapping['context_para_name'].'/', $key, $maches))
							{
								for($i = 1; $i < count($maches); $i++)
								{
									$param_name = str_replace('{'.$i.'}', $maches[$i], $mapping['local_para_name']);
									$local_params[$param_name] = $value;
								}
							}
						}
					}
				}
				$handler_class = $step_types[0]["step_handler"];
				$handler = new $handler_class();
				
				
				$sql = 'select * from '._DB_PREFIX_.'z_product_params where id_product = '.$id_product;
				$service_params = $db->ExecuteS($sql);
				$statusstring = $handler->getReadableStatusString($local_params, $service_params);
				
				
				$sql = 'select * from '._DB_PREFIX_.'z_step_type_ui where id_step_type = '.$id_step_type.' order by sequence';
				$step_ui = $db->ExecuteS($sql);
				
								
				$additional_uis = $handler->getAdditionalUIElements($service_params);
				
				if($additional_uis)
				{
					$step_ui = array_merge($step_ui, $additional_uis);
				}
				
				if($action_partner == 1)
				{
					$step_ui = null;
					$instruction = null;						
				}
				
				if(count($step_types) == 1)
				{
					$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '
							.$servie_product['id_service_type'].' order by id_step';
					
					$all_service_steps = $db->ExecuteS($sql);
					
					$product_name = Product::getProductName($id_product);
					
					
					$sql = 'select * from '._DB_PREFIX_.'z_service_public_params where id_service_type = '.$servie_product['id_service_type'];
					$all_public_params = $db->ExecuteS($sql);
					
					foreach($all_public_params as $all_public_param)
					{
						if($all_public_param['regex'])
						{
							foreach($context_params as $key => $value)
							{
								if(preg_match('/'.$all_public_param['param_name'].'/', $key, $maches));
								{
									for($i = 1; $i < count($maches); $i++)
									{
										$param_display_name = str_replace('{'.$i.'}', $maches[$i], $all_public_param['param_display_name']);
										$public_params[] = array(
												"value" => $value,
												"displayName" => $param_display_name
										); 
									}
								}
							}
						}else{
							$displayName = $all_public_param['param_display_name'];
							$value = $context_params[$all_public_param['param_name']];
							
							if(isset($value))
							{
								$public_params[] = array(
										"value" => $value,
										"displayName" => $displayName
								);
							}
						}
					}
					
					$transactions_ui = array("id_transaction" => $transaction['id_transaction'],
							"current_step" => $transaction['current_step'],
							"instruction" => $instruction,
							"servicetype" => $servie_product['id_service_type'],
							"description" => $description,
							"id_product" => $id_product,
							"steptype" => $id_step_type,
							"stephandler" => $step_types[0]["step_handler"],
							"ui_list" => $step_ui,
							"status_string" => $statusstring,
							"service_steps" => $all_service_steps,
							"product_name" => $product_name,
							"public_params" => isset($public_params)?$public_params:null,
							"tag" => (isset($context_params) && isset($context_params["tag"]))?$context_params["tag"]:''
					);
				}
			}
		}
		
		if($transactions_ui != null)
		{
			$this->smarty->assign(array(
					'transaction' => $transactions_ui
			));
			
			return $this->display(__FILE__, 'transactionactionpanel.tpl', $this->getCacheId('transactionactionpanel.tpl'));
		}else{
			return '';
		}
		
	}
	
	public function hookdisplayOrderDetail($params)
	{
		$db = Db::getInstance();
		
		$order = $params["order"];
		$id_order = $order->id;
		
		$sql = 'select * from '._DB_PREFIX_.'z_transaction where id_order = '.$id_order;
		
		$transactions = $db->ExecuteS($sql);
		
		if(!$transactions || count(transactions) == 0)
			return '';
		
		$output = '<div id="transation_action_panel" class="transation_action_panel">';
		//get all transactions linked to the order, it will also be linked to corresponding product
		foreach ($transactions as $transaction)		
		{
			$transaction_content = $this->displayTransactionDetail($transaction);
			$output = $output.$transaction_content;
		}
		
		return $output = $output."</div>";
		
		
	}
}	