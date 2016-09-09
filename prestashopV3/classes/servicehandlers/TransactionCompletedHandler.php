<?php

class TransactionCompletedHandlerCore
{
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		return AbstractHandler::PROCESS_SUCCESS;
	}
	public function getReadableStatusString($context_inputs, $service_parameters, $lang = null)
	{
		return null;
	}
	public function getAdditionalInputUIElements($context_inputs, $service_parameters)
	{
		return null;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
}