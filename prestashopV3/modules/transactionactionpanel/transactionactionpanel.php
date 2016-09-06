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
			&& $this->registerHook('displayAdminOrder') 
			&& $this->registerHook('ActionAdminControllerSetMedia')
			&& $this->registerHook('displayProductTabContent')
		);

		$this->_clearCache('*');
		
		$this->installTab('AdminTransaction', 'Transactions', 0);

		return $success;
	}
	
	private function installTab($tabClass, $tabName, $idTabParent)
	{
		$tab = new Tab();
		foreach (Language::getLanguages(false) as $language)
			$tab->name[$language['id_lang']] = $tabName;
			$tab->class_name = $tabClass;
			$tab->active = 1;
			$tab->module = $this->name;
			$tab->id_parent = $idTabParent;
			return $tab->save();
	}
	
	private function uninstallTab($tabClass)
	{
		$idTab = Tab::getIdFromClassName($tabClass);
		if ($idTab != 0) {
			$tab = new Tab($idTab);
			$tab->delete();
			return true;
		}
		return false;
	}
	
	public function uninstall()
	{
		$this->uninstallTab('AdminTransaction');
		
		if (!parent::uninstall())
			return false;
		return true;
	}
	
	public function displayProductDemoPanel($params){
		$db = Db::getInstance();
		
		$id_product = ((is_a($params['product'], 'Product'))?$params['product']->id:$params['product']);
		
		$sql = 'select * from '._DB_PREFIX_.'z_demo_transaction where id_product = '.$id_product;
		$demo_transactions = $db->ExecuteS($sql);

		$sql = 'select * from '._DB_PREFIX_.'z_transaction where id_transaction = "'.$demo_transactions[0]['id_transaction'].'"';
		$transactions = $db->ExecuteS($sql);

		$transacton = $transactions[0];

		$transacton['current_step'] = (isset($params['id_step']))? $params['id_step']:1;

		if($transacton['current_step'] != '1')
		{
			$input_nav_pre['ui_element_type'] = 'submit';
			$input_nav_pre['ui_element_name'] = 'transaction_nav_prev';
			$input_nav_pre['ui_element_label'] = '<  Browse Previous Step';
			$ui_list[] = $input_nav_pre;
		}
		
		$sql = 'select * from '._DB_PREFIX_.'z_service_product where id_product = '.$id_product;
		$servicetypes = $db->ExecuteS($sql);
		
		if(count($servicetypes) == 1)
		{
			$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '.$servicetypes[0]['id_service_type'].' and id_step = '.$transacton['current_step'];
			$nextsteps = $db->ExecuteS($sql);
				
			if(count($nextsteps) == 1)
			{
				$nextstep = $nextsteps[0]['next_step_id'];
					
				if($nextstep != -1)
				{
					$input_nav_nxt['ui_element_type'] = 'submit';
					$input_nav_nxt['ui_element_name'] = 'transaction_nav_next';
					$input_nav_nxt['ui_element_label'] = 'Browse Next Step  >';
					$ui_list[] = $input_nav_nxt;
				}
			}
		}
		

		return $this->displayTransactionDetail($transacton, false, false, $ui_list);
	}
	
	
	public function hookdisplayProductTabContent($params)
	{
		$demo_block = $this->displayProductDemoPanel($params);
			
		$this->smarty->assign(array(
					'transactionpanel' => $demo_block
			));
			
		return $this->display(__FILE__, 'transactiondemopanel.tpl', $this->getCacheId('transactiondemopanel.tpl'));
	}
	
	public function hookActionAdminControllerSetMedia()
	{
		$this->context->controller->addJS($this->_path.'views/js/transactionactionpanel.js');
		$this->context->controller->addJS($this->_path.'views/js/fotorama.js');
		$this->context->controller->addCSS($this->_path.'views/css/msform.css', 'all');
		$this->context->controller->addCSS($this->_path.'views/css/fotorama.css', 'all');
		$this->context->controller->addCSS($this->_path.'views/css/buttonstyle.css', 'all');
	}
	
	public function hookHeader($params)
	{
		$this->context->controller->addJS($this->_path.'views/js/transactionactionpanel.js');
		$this->context->controller->addJS($this->_path.'views/js/fotorama.js');
		$this->context->controller->addCSS($this->_path.'views/css/msform.css', 'all');
		$this->context->controller->addCSS($this->_path.'views/css/fotorama.css', 'all');
		$this->context->controller->addCSS($this->_path.'views/css/buttonstyle.css', 'all');
	}
	
	public function displayTransactionDetail($transaction, $showProductName = true, $showSubmit = true, $extendedButtons = null)
	{
		$is_admin = (defined('_PS_ADMIN_DIR_') || (int)(Tools::getValue("is_admin", 0)))?1:0;
		
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
				.' and id_service_type = '.$servie_product['id_service_type'].' and id_step = '.$transaction['current_step'].' and  direction = 0';
				
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
				
				$local_params['transaction_id'] = $transaction['id_transaction'];
				
				$handler_class = $step_types[0]["step_handler"];
				$handler = new $handler_class();
				
				
				$sql = 'select * from '._DB_PREFIX_.'z_product_params where id_product = '.$id_product;
				$service_params = $db->ExecuteS($sql);
				$statusstring = $handler->getReadableStatusString($local_params, $service_params);
				
				$step_ui = array();
				if($action_partner == $is_admin)
				{
					$sql = 'select * from '._DB_PREFIX_.'z_step_type_ui where id_step_type = '.$id_step_type.' order by sequence';
					$step_ui = $db->ExecuteS($sql);
					
					$additional_uis = $handler->getAdditionalInputUIElements($local_params, $service_params);
					if($additional_uis != null)
					{
						$step_ui = array_merge($step_ui, $additional_uis);
					}
				}else{
					$additional_uis = $handler->getAdditionalInputUIElementsNonAction($local_params, $service_params);
					if($additional_uis != null)
					{
						$step_ui = array_merge($step_ui, $additional_uis);
					}
				}
				
				$additional_uis = $handler->getAdditionalStatusUIElements($local_params, $service_params);
				if($additional_uis != null)
				{
					$step_ui = array_merge($step_ui, $additional_uis);
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
					
					$token = '';
					if($is_admin)
						$token=Tools::getAdminToken('AdminTransaction'.
								intval(Tab::getIdFromClassName('AdminTransaction')).
								intval(Context::getContext()->employee->id));
					
					$transactions_ui = array("id_transaction" => $transaction['id_transaction'],
							"current_step" => $transaction['current_step'],
							"instruction" => $instruction,
							"servicetype" => $servie_product['id_service_type'],
							"description" => $description,
							"id_product" => $id_product,
							"show_instruction" => ($action_partner == $is_admin),
							"steptype" => $id_step_type,
							"stephandler" => $step_types[0]["step_handler"],
							"ui_list" => $step_ui,
							"controller" => $is_admin?'AdminTransaction':'ProcessAction',
							"is_admin" => $is_admin,
							"token" => $token,
							"base_url" => _PS_BASE_URL_.__PS_BASE_URI__.($is_admin?'admin350/':''),
							"status_string" => $statusstring,
							"service_steps" => $all_service_steps,
							"product_name" => $product_name,
							"show_product_name" => $showProductName,
							"show_submit_button" => $showSubmit,
							"extended_buttons" => $extendedButtons,
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
		$order = $params["order"];
		$id_order = $order->id;
		
		return $this->displayOrderDetail($id_order);		
	}
	
	public function hookdisplayAdminOrder($params)
	{
		$orderid = (int)$_GET['id_order'];
		return $this->displayOrderDetail($orderid);
	}
	
	public function displayAllTransactions()
	{
		$db = Db::getInstance();
		
		$sql = 'select * from '._DB_PREFIX_.'z_transaction';
		
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
	
	public function displayOrderDetail($id_order)
	{
		$db = Db::getInstance();
		
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
	
	public function processActions(){
		$db = Db::getInstance ();
		
		$ajaxReturn = array();
		
		$handler_class = Tools::getValue ( "stephandler" );
		$handler = new $handler_class ();
		
		$service_type = Tools::getValue ( "servicetype" );
		$transaction_id = Tools::getValue ( "transaction_id" );
		$current_step = Tools::getValue ( "current_step" );
		$steptype = Tools::getValue ( "steptype" );
		$id_product = Tools::getValue ( "id_product" );
		$actionbutton = Tools::getValue ( "actionbutton" );
		
		$sql = 'select * from ' . _DB_PREFIX_ . 'z_transaction_context where id_transaction = "' . $transaction_id . '"';
		$context = $db->ExecuteS ( $sql );
		
		$sql = 'select * from ' . _DB_PREFIX_ . 'z_service_step_mapping where id_step_type = ' . $steptype . ' and id_service_type = ' . $service_type . ' and id_step = ' . $current_step . ' and direction = 0';
		
		$mappings = $db->ExecuteS ( $sql );
		foreach ( $mappings as $mapping ) {
			$context_param_value = $context [$mapping ['context_para_name']];
			if ($context_param_value != null) {
				$local_params [$mapping ['local_para_name']] = $context_param_value;
			}
		}
		
		$sql = 'select * from ' . _DB_PREFIX_ . 'z_product_params where id_product = ' . $id_product;
		$service_params = $db->ExecuteS ( $sql );
		
		$return = AbstractHandler::PROCESS_SUCCESS;
		
		if ($actionbutton == null)
			$return = $handler->processUIInputs ( $local_params, $outputs, $service_params, $errorinfo );
		else
			$return = $handler->processUIInputsNonAction ( $local_params, $outputs, $service_params, $errorinfo );
		
		if ($return == AbstractHandler::PROCESS_SUCCESS) {
			$sql = 'select * from ' . _DB_PREFIX_ . 'z_service_step_mapping where id_step_type = ' . $steptype . ' and id_service_type = ' . $service_type . ' and id_step = ' . $current_step . ' and direction = 1';
			
			$mappings = $db->ExecuteS ( $sql );
			
			foreach ( $mappings as $mapping ) {
				if (! $mapping ['regex']) {
					$local_param_value = $outputs [$mapping ['local_para_name']];
					if ($local_param_value != null) {
						$context [$mapping ['context_para_name']] = $local_param_value;
						
						$sql = "insert into " . _DB_PREFIX_ . "z_transaction_context (id_transaction, param_name, param_value) VALUES ('" . $transaction_id . "', '" . $mapping ['context_para_name'] . "', '" . $local_param_value . "') ON DUPLICATE KEY UPDATE " . "param_value = '" . $local_param_value . "'";
						$db->ExecuteS ( $sql );
					}
				} else {
					foreach ( $outputs as $key => $value ) {
						if (preg_match ( "/" . $mapping ['local_para_name'] . "/", $key, $maches )) {
							for($i = 1; $i < count ( $maches ); $i ++) {
								$param_name = str_replace ( '{' . $i . '}', $maches [$i], $mapping ['context_para_name'] );
								$context [$param_name] = $value;
								
								$sql = "insert into " . _DB_PREFIX_ . "z_transaction_context (id_transaction, param_name, param_value) VALUES ('" . $transaction_id . "', '" . $param_name . "', '" . $value . "') ON DUPLICATE KEY UPDATE " . "param_value = '" . $value . "'";
								$db->ExecuteS ( $sql );
							}
						}
					}
				}
			}
			
			if ($actionbutton == null) {
				$sql = 'select * from ' . _DB_PREFIX_ . 'z_service_step where id_service_type = ' . $service_type . ' and id_step = ' . $current_step;
				$nextsteps = $db->ExecuteS ( $sql );
				
				if (count ( $nextsteps ) == 1) {
					$nextstep = $nextsteps [0] ['next_step_id'];
					
					$sql = "update " . _DB_PREFIX_ . "z_transaction set current_step = '" . $nextstep . "' where id_transaction = '" . $transaction_id . "'";
					$db->ExecuteS ( $sql );
				}
			}
			
			$sql = 'select * from ' . _DB_PREFIX_ . 'z_transaction where id_transaction = "' . $transaction_id . '"';
			$transactions = $db->ExecuteS ( $sql );
			
			if ($transactions != null && count ( $transactions ) == 1) {
				
				$transacton = $transactions [0];
				
				$ajaxReturn ['next_step'] = $this->displayTransactionDetail ( $transacton );				
			}
		} else {
			$ajaxReturn ['errors'] = $errorinfo;
		}
		
		return $ajaxReturn;
	}
}	