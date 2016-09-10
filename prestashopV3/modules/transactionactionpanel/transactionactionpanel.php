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

		$sql = 'select * from '._DB_PREFIX_.'z_transaction where id_transaction = "'.$params['id_transaction'].'"';
		$transactions = $db->ExecuteS($sql);

		$transacton = $transactions[0];

		$transacton['current_step'] = (isset($params['id_step']))? $params['id_step']:1;
		
		$input_currentstep['ui_element_type'] = 'hidden';
		$input_currentstep['ui_element_name'] = 'current_step';
		$input_currentstep['ui_element_value'] = $transacton['current_step'];
		$ui_list[] = $input_currentstep;

		if($transacton['current_step'] != '1')
		{
			$input_nav_pre['ui_element_type'] = 'submit';
			$input_nav_pre['ui_element_name'] = 'transaction_nav_prev';
			$input_nav_pre['ui_element_label'] = '<  浏览上一步';
			$ui_list[] = $input_nav_pre;
		}
		
		
		$service_type = null;
		$current_step = null;
		$steptype = null;
		$id_product = null;
		$action_partner = null;
		$handler_class = null;
		
		$this->getRelevantInfo($params['id_transaction'], $service_type, $current_step,
				$steptype, $id_product, $action_partner, $handler_class);
		
		if($handler_class == null)
			return '';
		

		$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '.$service_type.' and id_step = '.$transacton['current_step'];
		$nextsteps = $db->ExecuteS($sql);
			
		if(count($nextsteps) == 1)
		{
			$nextstep = $nextsteps[0]['next_step_id'];
				
			if($nextstep != -1)
			{
				$input_nav_nxt['ui_element_type'] = 'submit';
				$input_nav_nxt['ui_element_name'] = 'transaction_nav_next';
				$input_nav_nxt['ui_element_label'] = '浏览下一步  >';
				$ui_list[] = $input_nav_nxt;
			}
		}		

		return $this->displayTransactionDetail($transacton, false, false, $ui_list);
	}
	
	
	public function hookdisplayProductTabContent($params)
	{
		$db = Db::getInstance();
		
		$id_product = $params['product']->id;
		
		$sql = 'select * from '._DB_PREFIX_.'z_demo_transaction where id_product = '.$id_product;
		$demo_transactions = $db->ExecuteS($sql);
		
		$params['id_transaction'] = $demo_transactions[0]['id_transaction'];
		
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
		
		$service_type = null;
		$current_step = $transaction['current_step']; //for the transaction demo
		$id_step_type = null;
		$id_product = null;
		$action_partner = null;
		$handler_class = null;
		
		$this->getRelevantInfo($transaction['id_transaction'], $service_type, $current_step,
				$id_step_type, $id_product, $action_partner, $handler_class);
		
		if($handler_class == null)
			return '';
				
		$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '
				.$service_type.' AND id_step = '.$current_step;
						
		$service_steps = $db->ExecuteS($sql);

		if(count($service_steps) == 0)
			return '';
		
		$service_step = $service_steps[0];
		$instruction = $service_step["instruction"];
		$description = $service_step["description"];
		
		$handler = new $handler_class();
		
		$local_params = array();
		
		$this->getlocalcontext($transaction['id_transaction'], $local_params, $service_type, $current_step, $id_step_type);		
				
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
		

		$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '
				.$service_type.' order by id_step';
		
		$all_service_steps = $db->ExecuteS($sql);
			
		$product_name = Product::getProductName($id_product);
			
			
		$sql = 'select * from '._DB_PREFIX_.'z_service_public_params where id_service_type = '.$service_type;
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
				"instruction" => $instruction,
				"description" => $description,
				"show_instruction" => ($action_partner == $is_admin),
				"current_step" => $current_step,
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
		
		if(!$transactions || count($transactions) == 0)
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
		
		if(!$transactions || count($transactions) == 0)
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
	
	
	public function getRelevantInfo($transaction_id, &$service_type, &$current_step, &$id_step_type, 
			                         &$id_product, &$action_partner, &$step_handler)
	{
		$db = Db::getInstance();
		
		$sql = 'select * from '._DB_PREFIX_.'z_transaction where id_transaction = "'.$transaction_id.'"';
		$transactions = $db->ExecuteS($sql);
		
		if(!$transactions || count($transactions) == 0)
			return;
		
		$transaction = $transactions[0];
		
		$current_step = ($current_step == null?$transaction['current_step']:$current_step);
		
		$id_product = $transaction["id_product"];
		$sql = 'select * from '._DB_PREFIX_.'z_service_product where id_product = '.$id_product;
			
		$service_products = $db->ExecuteS($sql);
		
		if(!$service_products || count($service_products) == 0)
			return;
		
		$servie_product = $service_products[0];
		
		$service_type = $servie_product['id_service_type'];
		
		
		$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '
				.$servie_product['id_service_type'].' AND id_step = '.$current_step;

		$service_steps = $db->ExecuteS($sql);

		if(!$service_steps || count($service_steps) == 0)
			return;
		
		$service_step = $service_steps[0];
		
		$id_step_type = $service_step["id_step_type"];
		$action_partner = $service_step["action_partner"];
		
		$sql = 'select * from '._DB_PREFIX_.'z_step_type where id_step_type = '.$id_step_type;
		$step_types = $db->ExecuteS($sql);
		
		if(!$step_types || count($step_types) == 0)
			return;
		
		$step_type = $step_types[0];
			
		$step_handler = $step_type["step_handler"];
	}
	
	public function getlocalcontext($id_transaction, &$local_params, $service_type, $current_step, $steptype)
	{
		$db = Db::getInstance ();
		
		$sql = 'select * from ' . _DB_PREFIX_ . 'z_transaction_context where id_transaction = "' . $id_transaction . '"';
		$context_raw = $db->ExecuteS ( $sql );
		
		$sql = 'select * from ' . _DB_PREFIX_ . 'z_service_step_mapping where id_step_type = ' . $steptype . ' and id_service_type = ' . $service_type . ' and id_step = ' . $current_step;
		$mappings = $db->ExecuteS ( $sql );
		
		$context = array();
		
		foreach($context_raw as $context_row)
		{
			$param_name = $context_row['param_name'];
			$context[$param_name] = $context_row['param_value'];
		}
		
		foreach($mappings as $mapping)
		{
			if(!$mapping['regex']) //normal mapping doesn't care direction
			{
				if(!array_key_exists($mapping['context_para_name'], $context))
					continue;
				
				$context_param_value = $context[$mapping['context_para_name']];
				$local_params[$mapping['local_para_name']] = $context_param_value;
				
			}else{
				if($mapping ['direction'] == 1) //regex style care direction
					continue;
				
				foreach($context as $key => $value)
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
		
		$sql = 'select * from ' . _DB_PREFIX_ . 'z_service_step_param where id_service_type = '.$service_type.' and id_step = '.$current_step ;
		$service_step_params = $db->ExecuteS ( $sql );
		
		foreach($service_step_params as $service_step_param)
		{
			$local_params[$service_step_param['param_name']] = $service_step_param['param_value'];
		}
		
		$local_params['transaction_id'] = $id_transaction;
	}
	
	public function processActions(){
		$is_admin = (defined('_PS_ADMIN_DIR_') || (int)(Tools::getValue("is_admin", 0)))?1:0;
		
		$db = Db::getInstance ();
		
		$ajaxReturn = array();
		
		$transaction_id = Tools::getValue ( "transaction_id" );
		
		$service_type = null;
		$current_step = null;
		$steptype = null;
		$id_product = null;
		$action_partner = null;
		$handler_class = null;
		
		$this->getRelevantInfo($transaction_id, $service_type, $current_step, 
				               $steptype, $id_product, $action_partner, $handler_class);
		
		if($handler_class == null)
			return $ajaxReturn ['errors'] = 'Invalid Action Triggered';
		
		$handler = new $handler_class ();
		$actionbutton = Tools::getValue ( "actionbutton" );		
		
		$sql = 'select * from ' . _DB_PREFIX_ . 'z_product_params where id_product = ' . $id_product;
		$service_params = $db->ExecuteS ( $sql );	
		
		$local_params = array();
		
		$this->getlocalcontext($transaction_id, $local_params, $service_type, $current_step, $steptype);
		
		$return = AbstractHandler::PROCESS_SUCCESS;
		
		if ($action_partner == $is_admin)
			$return = $handler->processUIInputs ( $local_params, $outputs, $service_params, $errorinfo );
		else
			$return = $handler->processUIInputsNonAction ( $local_params, $outputs, $service_params, $errorinfo );
		
		if ($return == AbstractHandler::PROCESS_SUCCESS) {
			$sql = 'select * from ' . _DB_PREFIX_ . 'z_service_step_mapping where id_step_type = ' . $steptype . ' and id_service_type = ' . $service_type . ' and id_step = ' . $current_step . ' and direction = 1';
			
			$mappings = $db->ExecuteS ( $sql );
			
			foreach ( $mappings as $mapping ) {
				if (! $mapping ['regex']) {
					if(!array_key_exists($mapping ['local_para_name'], $outputs))
						continue;
					
					$local_param_value = $outputs [$mapping ['local_para_name']];
					$context [$mapping ['context_para_name']] = $local_param_value;
						
					$sql = "insert into " . _DB_PREFIX_ . "z_transaction_context (id_transaction, param_name, param_value) VALUES ('" . $transaction_id . "', '" . $mapping ['context_para_name'] . "', '" . $local_param_value . "') ON DUPLICATE KEY UPDATE " . "param_value = '" . $local_param_value . "'";
					$db->ExecuteS ( $sql );					
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
			
			if ($action_partner == $is_admin && $actionbutton == null) {
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