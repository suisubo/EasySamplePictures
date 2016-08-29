<?php

class ObtainShippingLabelHandlerCore extends AbstractHandler
{
    public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){
    	$shipping_label = Tools::getValue("tracking_label");
    	$shipping_carrier = Tools::getValue("shipping_carrier");
    	$tag = Tools::getValue("tag");
    	
    	if(strlen($shipping_label) > 0 && strlen($shipping_carrier) > 0)
    	{
    		$outputs["shipping_carrier"] = $shipping_carrier;
    		$outputs["tracking_label"] = $shipping_label;
    		$outputs["tag"] = $tag;
    		
    		foreach($service_parameters as $service_parameter)
    		{
    			if($service_parameter['param_name'] == 'sample_num')
    			{
    				$num_example = (int)$service_parameter['param_value'];
    		
    				for($i = 1; $i <= $num_example; $i++)
    				{
    					$outputs['sample_url'.$i] = Tools::getValue('sample_url'.$i);
    				}
    			}
    		}
    		 
    		return AbstractHandler::PROCESS_SUCCESS;
    	}else
    	{ 	
    		$error_info = "Please put in valid tracking label.";
    		return AbstractHandler::PROCESS_FAIL;
    	}
    		
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $lang = null){
		return '';
	}
	
	public function getAdditionalUIElements($service_parameters){
		foreach($service_parameters as $service_parameter)
		{
			if($service_parameter['param_name'] == 'sample_num')
			{
				$num_example = (int)$service_parameter['param_value'];
				
				for($i = 1; $i <= $num_example; $i++)
				{
					$input['ui_element_type'] = 'text';
					$input['ui_element_name'] = 'sample_url'.$i;
					$input['ui_element_label'] = 'URL for sample '.$i;
					$input['id_step_type'] = 1;
						
					$ui_list[] = $input;
				}
				
				return $ui_list;
			}
		}		
	}
}