<?php

abstract class AbstractHandlerCore
{
	const PROCESS_SUCCESS = 0;
	const PROCESS_FAIL = 1;
	
	const STEP_INPUT_PARTNER_CUSTOMER = 0;
	const STEP_INPUT_PARTNER_VENDOR = 1;	
	
	abstract public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info);
	abstract public function getReadableStatusString($context_inputs, $service_parameters, $lang);
	abstract public function getAdditionalInputUIElements($context_inputs, $service_parameters);
	abstract public function getAdditionalStatusUIElements($context_inputs, $service_parameters);
}
