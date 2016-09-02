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
    					$outputs['sample_tag'.$i] = Tools::getValue('sample_tag'.$i);
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
		return null;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
	
	public function getAdditionalInputUIElements($context_inputs, $service_parameters){
		foreach($service_parameters as $service_parameter)
		{
			if($service_parameter['param_name'] == 'sample_num')
			{
				$num_example = (int)$service_parameter['param_value'];
				
				for($i = 1; $i <= $num_example; $i++)
				{
					$input_sample_tag['ui_element_type'] = 'text';
					$input_sample_tag['ui_element_name'] = 'sample_tag'.$i;
					$input_sample_tag['ui_element_label'] = 'Name for sample '.$i;
					$input_sample_tag['id_step_type'] = 1;					
					$ui_list[] = $input_sample_tag;
					
					$input_url['ui_element_type'] = 'text';
					$input_url['ui_element_name'] = 'sample_url'.$i;
					$input_url['ui_element_label'] = 'URL for sample '.$i;
					$input_url['id_step_type'] = 1;						
					$ui_list[] = $input_url;
				}
				
				return $ui_list;
			}
		}		
	}
}