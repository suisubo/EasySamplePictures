<?php

class MonitorShippingHandlerCore extends AbstractHandler
{
	public $after_shipping_api_key = '5db673d0-648c-4e4e-877b-44ab330a9306';
	
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    		
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $nonaction){
		
		if(array_key_exists('aftership_id1', $context_inputs))
		{
			$trackings = new Trackings($this->after_shipping_api_key);
			$trackings->get_by_id('53df4a90868a6df243b6efd8', array(
					'fields' => 'customer_name'
			));
		}
		
		return null;
	}
	
	public function processUIInputsNonAction($context_inputs, &$outputs, $service_parameters, &$error_info)
	{		
		$actionbutton = Tools::getValue ( 'actionbutton' );
		if ($actionbutton == 'submit_shipping_info') {
			$shipping_label = Tools::getValue('shipping_label1');
			
			$trackings = new Trackings($this->after_shipping_api_key);
			
			try{
				$response = $trackings->create($shipping_label);				
				
				if(array_key_exists('meta', $response) && $response['meta']['code'] == 201)
				{
					$outputs['shipping_label1'] = $shipping_label;
					$outputs['shipping_carrier1'] = $response['data']['tracking']['slug'];
					$outputs['aftership_id1'] = $response['data']['tracking']['id'];
				}
				
			}catch(Exception $e){
				$error_info[] = '无效的运单信息：'.$shipping_label.' '.$e->getMessage();
				return AbstractHandler::PROCESS_FAIL;
			}
		}
	}
		
	
	public function getAdditionalInputUIElements($context_inputs, $service_parameters){
		$ui = '<input type="text" placeholder="运单号1" name="shipping_label1"/><br>';
		$ui = $ui.'<input type="text" placeholder="商品1" name="shipping_content1"/><br><br>';
		
		
		$ui = $ui.'<input type="text" placeholder="运单号2" name="shipping_label2"/><br>';
		$ui = $ui.'<input type="text" placeholder="商品2" name="shipping_content2"/><br><br>';
				
		$ui = $ui.'<input type="text" placeholder="运单号3" name="shipping_label3"/><br>';
		$ui = $ui.'<input type="text" placeholder="商品3" name="shipping_content3"/><br><br>';
				
		$ui = $ui.'<input type="text" placeholder="运单号4" name="shipping_label4"/><br>';
		$ui = $ui.'<input type="text" placeholder="商品4" name="shipping_content4"/><br><br>';
				
		$ui = $ui.'<input type="text" placeholder="运单号5" name="shipping_label5"/><br>';
		$ui = $ui.'<input type="text" placeholder="商品5" name="shipping_content5"/><br><br>';
				
		$ui = $ui.'<input type="button" class="action-button transaction_nonaction" name="submit_shipping_info" value="提交/修改运单信息信息"/><br>';
		
		$payment['ui_element_type'] = 'custom';
		$payment['ui_element_custom_content'] = $ui;
		
		$ui_list[] = $payment;
		
		return $ui_list;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
	
	public function getAdditionalInputUIElementsNonAction($context_inputs, $service_parameters)
	{
		return null;
	}
}
