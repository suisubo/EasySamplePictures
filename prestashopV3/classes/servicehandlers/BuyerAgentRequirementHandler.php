<?php

class BuyerAgentRequirementHandlerCore extends AbstractHandler
{
    public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info){    	
    		
    }
    
	public function getReadableStatusString($context_inputs, $service_parameters, $nonaction){
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
		return null;
	}
}