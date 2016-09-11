<?php

class MonitorShippingHandlerCore extends AbstractHandler
{
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    		
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $nonaction){
		
		foreach($service_parameters as $service_parameter)
		{
			if($service_parameter['param_name'] == 'sample_num')
			{
				$num_example = (int)$service_parameter['param_value'];
		
				for($i = 1; $i <= $num_example; $i++)
				{
					$output = $output.$context_inputs['sample_url'.$i];
				}
		
				return 'Package is on the route:'.$context_inputs['tracking_label'].$output;
			}
		}
		
		return null;
	}
	
	public function getAdditionalInputUIElements($context_inputs, $service_parameters){
		$ui = '<input type="text" placeholder="运单号" name="shipping_label"/><br>';
		$ui = '<input type="text" placeholder="快递公司" name="shipping_carrier"/><br>';
		$ui = $ui.'<input type="button" class="action-button transaction_nonaction" name="submit_shipping_info" value="提交运单信息信息"/>';
		
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
