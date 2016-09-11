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
// 				if (Tools::strtoupper ( Tools::getValue ( 'payment_status' ) ) == 'COMPLETED')
// 					$order_status = ( int ) Configuration::get ( 'PS_OS_PAYMENT' );
// 				elseif (Tools::strtoupper ( Tools::getValue ( 'payment_status' ) ) == 'PENDING')
// 					$order_status = ( int ) Configuration::get ( 'PS_OS_PAYPAL' );
// 				elseif (Tools::strtoupper ( Tools::getValue ( 'payment_status' ) ) == 'REFUNDED')
// 					$order_status = ( int ) Configuration::get ( 'PS_OS_REFUND' );
// 				else
// 					$order_status = ( int ) Configuration::get ( 'PS_OS_ERROR' );
				
				$outputs['payment_method'] = 'Paypal';
				$outputs['payment_amount'] = Tools::getValue ( 'mc_gross' );
				$outputs['payment_txn_id'] = Tools::getValue ( 'txn_id' );
				$outputs['payment_date'] = Tools::getValue ( 'payment_date' );
				$outputs['payment_currency'] = Tools::getValue ( 'mc_currency' );
				$outputs['payment_fee'] = Tools::getValue ( 'mc_fee' );
				$outputs['payment_protection_eligibility'] = Tools::getValue ( 'protection_eligibility' );
				$outputs['payment_address_status'] = Tools::getValue ( 'address_status' );
				$outputs['payment_payer_id'] = Tools::getValue ( 'payer_id' );
				$outputs['payment_payer_status'] = Tools::getValue ( 'payer_status' );
				$outputs['payment_payer_email'] = Tools::getValue ( 'payer_email' );
				$outputs['payment_receipt_id'] = Tools::getValue ( 'receipt_id' );
				$outputs['payment_ipn_track_id'] = Tools::getValue ( 'ipn_track_id' );
				$outputs['payment_verify_sign'] = Tools::getValue ( 'verify_sign' );
				$outputs['payment_mode'] = Tools::getValue ( 'test_ipn' )?'Test (Sandbox)' : 'Live';
				$outputs['payment_status'] = Tools::getValue ( 'payment_status' );
				$outputs['payment_type'] = Tools::getValue ( 'payment_type' );
				
					
				$payment_type = Tools::getValue('payment_type');
				$transaction_log =     'Transaction ID: ' . $custom [0] . '
						                step ID: ' . $custom [1] . '
								        step ID: ' . $custom [1] . '
								        Paypal Transaction ID: ' . Tools::getValue ( 'txn_id' ) . '
								        payment status: '. Tools::getValue ( 'payment_status' ) .'
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
				
				error_log($transaction_log);
				
				return AbstractHandler::PROCESS_SUCCESS;
			}else{
				error_log('failed to invoke');
			}
			
			if ($actionbutton == 'submit_payment_transaction') {
				if(Tools::getValue('payment_method') == 'alipay' && (!array_key_exists('payment_method', $context_inputs)))
				{
					if(Tools::getValue ( 'txn_id' ) != null && strlen(Tools::getValue ( 'txn_id' )) > 15)
					{
						$outputs['payment_txn_id'] = Tools::getValue ( 'txn_id' );
						$outputs['payment_method'] = 'Alipay';
					}else{
						$error_info = "请输入有效的交易流水号.";
						return AbstractHandler::PROCESS_FAIL;
					}
				}else{
					$error_info = "您已经通过Paypal付款，请等待确认.";
					return AbstractHandler::PROCESS_FAIL;
				}
				
				return AbstractHandler::PROCESS_SUCCESS;
			}
				
		}
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $nonaction){
		$Status = '<table>';
		if(array_key_exists('payment_method', $context_inputs))
		{
			$Status = $Status.'<tr><td ><b>付款信息收到，等待验证<b></td></tr>';
			
			$Status = $Status.'<tr><td ><b>付款方式：<b></td><td >'. $context_inputs['payment_method'] .'</td></tr>';
			$Status = $Status.'<tr><td ><b>付款金额：<b></td><td >'. $context_inputs['payment_amount'] .'</td></tr>';
			$Status = $Status.'<tr><td ><b>货币单位：<b></td><td >'. $context_inputs['payment_currency'] .'</td></tr>';			
			$Status = $Status.'<tr><td ><b>付款方：<b></td><td >'. $context_inputs['payment_payer_email'] .'</td></tr>';
			$Status = $Status.'<tr><td ><b>日期：<b></td><td >'. $context_inputs['payment_date'] .'</td></tr>';
			
			if(!$nonaction)
			{
				$Status = $Status.'<tr><td ><b>费用：<b></td><td >'. $context_inputs['payment_fee'] .'</td></tr>';
				$Status = $Status.'<tr><td ><b>交易编号：<b></td><td >'. $context_inputs['payment_txn_id'] .'</td></tr>';
				$Status = $Status.'<tr><td ><b>付款状态：<b></td><td >'. $context_inputs['payment_status'] .'</td></tr>';
			}
			
		} 
		
		$Status = $Status.'</table>'; 
				
		return $Status;
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
		
		$alippay_transaction_id = null;
		
		if($context_inputs['payment_method'] == 'Alipay')
		{
			if(array_key_exists('payment_txn_id', $context_inputs))
			{
				$alippay_transaction_id = $context_inputs['payment_txn_id'];
			}
		}
		
		Context::getContext()->smarty->assign(array(
				'paypal_usa_action' => 'https://www'.(Configuration::get('PAYPAL_USA_SANDBOX') ? '.sandbox' : '').'.paypal.com/cgi-bin/webscr',
				'total' => $total,
				'custom' => $custom,
				'alipay_trarnsaction_id' => $alippay_transaction_id,
				'transaction_id' => $context_inputs['transaction_id'],
				'cancel_url' => Context::getContext()->link->getPageLink('order.php',''),
				'notify_url' => $link,
				'return_url' => Context::getContext()->link->getPageLink('order.php',''),
				'base_url' => __PS_BASE_URI__
		
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
		
		$ui = '<input type="text" placeholder="支付宝流水号" name="txn_id"/><br>';
		$ui = $ui.'<input type="button" class="action-button transaction_nonaction" name="submit_payment_transaction" value="提交支付信息"/>';
		
		$payment['ui_element_type'] = 'custom';
		$payment['ui_element_custom_content'] = $ui;
		
		$ui_list[] = $payment;
		
		return $ui_list;
	}
}