<?php

class MakePaymentHandlerCore extends AbstractHandler
{
    public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    	

    }
    
    public function processUIInputsNonAction($context_inputs, &$outputs, $service_parameters, &$error_info)
    {
    	$actionbutton = Tools::getValue ( 'actionbutton' );
		if ($actionbutton == 'paypal_notif') {
			/* Step 1 - Double-check that the order sent by PayPal is valid one */
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, 'https://www.' . (Configuration::get ( 'PAYPAL_USA_SANDBOX' ) ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr' );
			curl_setopt ( $ch, CURLOPT_VERBOSE, 0 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, 'cmd=_notify-validate&' . http_build_query ( $_POST ) );
			$response = curl_exec ( $ch );
			curl_close ( $ch );
			$context = Context::getContext ();
			
			if ($response == 'VERIFIED') {
				$moduleInstance = Module::getInstanceByName ( 'transactionactionpanel' );
				
				$service_type = null;
				$current_step = null;
				$steptype = null;
				$id_product = null;
				$action_partner = null;
				$handler_class = null;
				
				$moduleInstance->getRelevantInfo ( $context_inputs ['transaction_id'], $service_type, $current_step, $steptype, $id_product, $action_partner, $handler_class );
				
				if ($handler_class == null)
					return AbstractHandler::PROCESS_FAIL;
				
				$custom = explode ( ';', Tools::getValue ( 'custom' ) );
				
				if (count ( $custom ) != 2)
					return AbstractHandler::PROCESS_FAIL;
				
				if ($context_inputs ['transaction_id'] != $custom [0] || $current_step != $custom [1])
					return AbstractHandler::PROCESS_FAIL;
				
				$currency = Tools::getValue ( 'mc_currency' );
				
				/* Step 4 - Determine the order status in accordance with the response from PayPal */
				if (Tools::strtoupper ( Tools::getValue ( 'payment_status' ) ) == 'COMPLETED')
					$order_status = ( int ) Configuration::get ( 'PS_OS_PAYMENT' );
				elseif (Tools::strtoupper ( Tools::getValue ( 'payment_status' ) ) == 'PENDING')
					$order_status = ( int ) Configuration::get ( 'PS_OS_PAYPAL' );
				elseif (Tools::strtoupper ( Tools::getValue ( 'payment_status' ) ) == 'REFUNDED')
					$order_status = ( int ) Configuration::get ( 'PS_OS_REFUND' );
				else
					$order_status = ( int ) Configuration::get ( 'PS_OS_ERROR' );
				
				$payment_type = Tools::getValue('payment_type');
				$message = 'Transaction ID: ' . Tools::getValue ( 'txn_id' ) . '
								Payment Type: ' . $payment_type . '
								Order time: ' . Tools::getValue ( 'payment_date' ) . '
								Final amount charged: ' . Tools::getValue ( 'mc_gross' ) . '
								Currency code: ' . Tools::getValue ( 'mc_currency' ) . '
								PayPal fees: ' . ( float ) Tools::getValue ( 'mc_fee' ) . '
								Protection Eligibility: ' . Tools::getValue ( 'protection_eligibility' ) . '
								address status: ' . Tools::getValue ( 'address_status' ) . '
								payer_id: ' . Tools::getValue ( 'payer_id' ) . '
								payer_status: ' . Tools::getValue ( 'payer_status' ) . '
								payer_email: ' . Tools::getValue ( 'payer_email' ) . '
								receipt_id: ' . Tools::getValue ( 'receipt_id' ) . '
								ipn_track_id: ' . Tools::getValue ( 'ipn_track_id' ) . '
								verify_sign: ' . Tools::getValue ( 'verify_sign' ) . '
								Mode: ' . (Tools::getValue ( 'test_ipn' ) ? 'Test (Sandbox)' : 'Live');
				
				error_log($message);
				
				return AbstractHandler::PROCESS_SUCCESS;
			}else{
				error_log('failed to invoke');
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