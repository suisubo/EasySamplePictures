<?php

class MonitorShippingHandlerCore extends AbstractHandler
{
	public $after_shipping_api_key = '5db673d0-648c-4e4e-877b-44ab330a9306';
	
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    		
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $nonaction)
	{
		for($i = 1; $i <= 5;$i++)
		{
			if(array_key_exists('aftership_id'.$i, $context_inputs))
			{
				$trackings = new Trackings($this->after_shipping_api_key);
				$checkpoints=  $trackings->get_by_id('53df4a90868a6df243b6efd8', array(
						'fields' => 'checkpoints'
				));
			}
			
			return null;
		}
		
	}
	
	public function processUIInputsNonAction($context_inputs, &$outputs, $service_parameters, &$error_info)
	{		
		$actionbutton = Tools::getValue ( 'actionbutton' );
		$error_info = array();
		if ($actionbutton == 'submit_shipping_info') {
			$trackings = new Trackings($this->after_shipping_api_key);
			for($i = 1; $i <= 5;$i++)
			{
				$shipping_label = Tools::getValue('shipping_label'.$i);
				$shipping_content = Tools::getValue('shipping_content'.$i);
				
				if($shipping_label == null || strlen($shipping_label) == 0)
				{
					$outputs['shipping_label'.$i] = '';
					$outputs['shipping_carrier'.$i] = '';
					$outputs['aftership_id'.$i] = '';
					
					continue;
				}
				
				if($shipping_content == null || strlen($shipping_content) == 0)
				{
					$error_info[] = '运单号'.$i.' &lt;'.$shipping_label.$i.'&gt; 的描述为空 ';
					continue;
				}
				
				try{
					$response = $trackings->create($shipping_label);
				
					if(array_key_exists('meta', $response) && $response['meta']['code'] == 201)
					{
						$outputs['shipping_label'.$i] = $shipping_label;
						$outputs['shipping_carrier'.$i] = $response['data']['tracking']['slug'];
						$outputs['aftership_id'.$i] = $response['data']['tracking']['id'];
					}
				
				}catch(Exception $e){
					$error_info[] = '无效的运单信息：&lt;'.$shipping_label.$i.'&gt; '.$e->getMessage();					
				}
			}
			
			if(count($error_info) > 0)
			{
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
