<?php

class MakePaymentHandlerCore extends AbstractHandler
{
    public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    	

    }
    
    public function processUIInputsNonAction($context_inputs, &$outputs, $service_parameters, &$error_info)
    {
    	$actionbutton = Tools::getValue('actionbutton');
    	if($actionbutton == 'paypal_notif')
    	{
    		    /* Step 1 - Double-check that the order sent by PayPal is valid one */
		    	$ch = curl_init();
		    	curl_setopt($ch, CURLOPT_URL, 'https://www.'.(Configuration::get('PAYPAL_USA_SANDBOX') ? 'sandbox.' : '').'paypal.com/cgi-bin/webscr');
		    	curl_setopt($ch, CURLOPT_VERBOSE, 0);
		    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    	curl_setopt($ch, CURLOPT_POST, true);
		    	curl_setopt($ch, CURLOPT_POSTFIELDS, 'cmd=_notify-validate&'.http_build_query($_POST));
		    	$response = curl_exec($ch);
		    	curl_close($ch);
		    	$context = Context::getContext();    
		    	
		    	if ($response == 'VERIFIED')
		    	{
		    		$moduleInstance = Module::getInstanceByName('transactionactionpanel');
		    		
		    		$service_type = null;
		    		$current_step = null;
		    		$steptype = null;
		    		$id_product = null;
		    		$action_partner = null;
		    		$handler_class = null;
		    		
		    		$moduleInstance->getRelevantInfo($context_inputs['transaction_id'], $service_type, $current_step,
		    				$steptype, $id_product, $action_partner, $handler_class);
		    		
		    		if($handler_class == null)
		    			return '';
		    		
		    		$custom = explode(';', Tools::getValue('custom'));
		    		
		    		if (count($custom) != 2)
		    			return '';
		    		
		    		if($context_inputs['transaction_id'] != $custom[0] || $current_step != $custom[1])
		    			return '';
		    		
		    			
		    		
		    	}
    	}
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $lang = null){
		return null;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
	
	public function getAdditionalInputUIElements($context_inputs, $service_parameters){
		return null;
	}
	
	public function getAdditionalInputUIElementsNonAction($context_inputs, $service_parameters)
	{
		$total = isset($context_inputs['payment_ratio'])? ((((float)$context_inputs['payment_ratio'])/100) * (float)$context_inputs['amount']):((float)$context_inputs['amount']);
		
		$params['actionbutton'] = 'paypal_notif';
		$params['transaction_id'] = $context_inputs['transaction_id'];
		$params['ajax'] = 1;
		
		$moduleInstance = Module::getInstanceByName('transactionactionpanel');
		
		$service_type = null;
		$current_step = null;
		$steptype = null;
		$id_product = null;
		$action_partner = null;
		$handler_class = null;
		
		$moduleInstance->getRelevantInfo($context_inputs['transaction_id'], $service_type, $current_step,
				$steptype, $id_product, $action_partner, $handler_class);
		
		if($handler_class == null)
			return '';
		
		
		$link = Context::getContext()->link->getModuleLink('transactionactionpanel', 'processaction', $params, null);
		
		$custom = $context_inputs['transaction_id'].';'.$current_step;
		
		Context::getContext()->smarty->assign(array(
				'paypal_usa_action' => 'https://www'.(Configuration::get('PAYPAL_USA_SANDBOX') ? '.sandbox' : '').'.paypal.com/cgi-bin/webscr',
				'total' => $total,
				'custom' => $custom,
				'cancel_url' => Context::getContext()->link->getPageLink('order.php',''),
				'notify_url' => $link,
				'return_url' => Context::getContext()->link->getPageLink('order.php',''),
		
		));
		
		$template = Context::getContext()->smarty->createTemplate(__DIR__.'/templates/paypalstandard.tpl',
																  null,
																  null,
																  Context::getContext()->smarty
																  );
		
		$content = $template->fetch();
		
		$payment['ui_element_type'] = 'form';
		$payment['ui_element_form'] = $content;

		$ui_list[] = $payment;
		
		return $ui_list;
	}
}