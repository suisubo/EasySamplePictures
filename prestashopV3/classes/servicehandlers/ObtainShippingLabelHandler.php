<?php

class ObtainShippingLabelHandlerCore extends AbstractHandler
{
    public function processUIInputs($context_inputs, &$outputs, &$error_info){
    	$shipping_label = Tools::getValue("tracking_label");
    	$shipping_carrier = Tools::getValue("shipping_carrier");
    	
    	if(strlen($shipping_label) > 0 && strlen($shipping_carrier) > 0)
    	{
    		$outputs["shipping_carrier"] = $shipping_carrier;
    		$outputs["tracking_label"] = $shipping_label;
    		 
    		return AbstractHandler::PROCESS_SUCCESS;
    	}else
    	{ 	
    		$error_info = "Please put in valid tracking label.";
    		return AbstractHandler::PROCESS_FAIL;
    	}
    		
    }
    
	public function getReadableStatusString($context_inputs, $lang = null){
		return '';
	}
}