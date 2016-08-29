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
	
	public function displayAjax()
	{
		$db = Db::getInstance();
		
		$handler_class = Tools::getValue("stephandler");
		$handler = new $handler_class();
		
		$service_type = Tools::getValue("servicetype");
		$transaction_id = Tools::getValue("transaction_id");
		$current_step = Tools::getValue("current_step");
		$steptype = Tools::getValue("steptype");
		$id_product = Tools::getValue("id_product");
		
		
		$sql = 'select * from '._DB_PREFIX_.'z_transaction_context where id_transaction = "'.$transaction_id.'"';
		$context = $db->ExecuteS($sql);
		
		$sql = 'select * from '._DB_PREFIX_.'z_service_step_mapping where id_step_type = '.$steptype
		       .' and id_service_type = '.$service_type.' and id_step = '.$current_step.' and direction = 0';
		
		$mappings = $db->ExecuteS($sql);
		foreach($mappings as $mapping)
		{
			$context_param_value = $context[$mapping['context_para_name']];
			if($context_param_value != null)
			{
				$local_params[$mapping['local_para_name']] = $context_param_value;
			}
		}
		
		$sql = 'select * from '._DB_PREFIX_.'z_product_params where id_product = '.$id_product;
		$service_params = $db->ExecuteS($sql);
		
		$return = $handler->processUIInputs($local_params, $outputs, $service_params, $errorinfo);
		
		
		
		if($return == AbstractHandler::PROCESS_SUCCESS)
		{
			$sql = 'select * from '._DB_PREFIX_.'z_service_step_mapping where id_step_type = '.$steptype
			.' and id_service_type = '.$service_type.' and id_step = '.$current_step.' and direction = 1';
			
			$mappings = $db->ExecuteS($sql);
			
			foreach($mappings as $mapping)
			{
				if(!$mapping['regex'])
				{
					$local_param_value = $outputs[$mapping['local_para_name']];
					if($local_param_value != null)
					{
						$context[$mapping['context_para_name']] = $local_param_value;
							
						$sql = "insert into "._DB_PREFIX_."z_transaction_context (id_transaction, param_name, param_value) VALUES ('"
								.$transaction_id."', '".$mapping['context_para_name']."', '".$local_param_value."') ON DUPLICATE KEY UPDATE ".
								"param_value = '".$local_param_value."'";
								$db->ExecuteS($sql);
					}
				}else{
					foreach($outputs as $key => $value)
					{
						if(preg_match("/".$mapping['local_para_name']."/", $key, $maches))
						{
							for($i = 1; $i < count($maches); $i++)
							{
								$param_name = str_replace('{'.$i.'}', $maches[$i], $mapping['context_para_name']);
								$context[$param_name] = $value;
								
								$sql = "insert into "._DB_PREFIX_."z_transaction_context (id_transaction, param_name, param_value) VALUES ('"
										.$transaction_id."', '".$param_name."', '".$value."') ON DUPLICATE KEY UPDATE ".
										"param_value = '".$value."'";
										$db->ExecuteS($sql);
							}
						}
					}
				}
			}
			
			$sql = 'select * from '._DB_PREFIX_.'z_service_step where id_service_type = '.$service_type.' and id_step = '.$current_step;
			$nextsteps = $db->ExecuteS($sql);
			
			if(count($nextsteps) == 1)
			{
				$nextstep = $nextsteps[0]['next_step_id'];
				
				$sql = "update "._DB_PREFIX_."z_transaction set current_step = '".$nextstep."' where id_transaction = '".$transaction_id."'";
				$db->ExecuteS($sql);
				
				$sql = 'select * from '._DB_PREFIX_.'z_transaction where id_transaction = "'.$transaction_id.'"';
				$transactions = $db->ExecuteS($sql);
				
				if($transactions != null && count($transactions) == 1)
				{
					$moduleInstance = Module::getInstanceByName('transactionactionpanel');
					$transacton = $transactions[0];
					
					$ajaxReturn['next_step'] = $moduleInstance->displayTransactionDetail($transacton);
					
					$this->ajaxDie(Tools::jsonEncode($ajaxReturn));
				}
			}
		}else{
			$ajaxReturn['errors'] = $errorinfo;
			$this->ajaxDie(Tools::jsonEncode($ajaxReturn));
		}
		
	}
	
}