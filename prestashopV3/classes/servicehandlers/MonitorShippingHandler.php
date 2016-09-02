<?php

class MonitorShippingHandlerCore extends AbstractHandler
{
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    		
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $lang = null){
		
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
		return null;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
}
