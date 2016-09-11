<?php

abstract class AbstractHandlerCore
{
	const PROCESS_SUCCESS = 0;
	const PROCESS_FAIL = 1;
	
	const STEP_INPUT_PARTNER_CUSTOMER = 0;
	const STEP_INPUT_PARTNER_VENDOR = 1;	
	
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		return AbstractHandler::PROCESS_SUCCESS;
	}
	
	public function processUIInputsNonAction($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		return AbstractHandler::PROCESS_SUCCESS;
	}
	
    public function getReadableStatusString($context_inputs, $service_parameters, $nonaction)
    {
    	return null;
    }
	public function getAdditionalInputUIElements($context_inputs, $service_parameters)
	{
		return null;
	}
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters)
	{
		return null;
	}
	
	public function getAdditionalInputUIElementsNonAction($context_inputs, $service_parameters)
	{
		return null;
	}
}
