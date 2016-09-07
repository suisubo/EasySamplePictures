<?php

class MakePaymentHandlerCore extends AbstractHandler
{
    public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    	
    		
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $lang = null){
		return null;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
	
	public function getAdditionalInputUIElements($context_inputs, $service_parameters){
		$total = isset($context_inputs['payment_ratio'])? ((((float)$context_inputs['payment_ratio'])/100) * (float)$context_inputs['amount']):((float)$context_inputs['amount']);
		
		Context::getContext()->smarty->assign(array(
				'paypal_usa_action' => 'https://www'.(Configuration::get('PAYPAL_USA_SANDBOX') ? '.sandbox' : '').'.paypal.com/cgi-bin/webscr',
				'total' => $total,
				'transaction_id' => $context_inputs['transaction_id'],
				'cancel_url' => Context::getContext()->link->getPageLink('order.php',''),
				'notify_url' => Context::getContext()->link->getPageLink('order.php',''),
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
	
	public function getAdditionalInputUIElementsNonAction($context_inputs, $service_parameters)
	{
		return null;
	}
}