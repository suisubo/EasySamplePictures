<?php

abstract class AbstractHandlerCore
{
	const UI_REQUEST_CONFIRMATION = 1;
	const UI_REQUEST_TEXT_INPUT = 2;
	const UI_REQUEST_FILE_UPLOAD = 3;
	const UI_REQUEST_FILE_DOWNLOAD = 4;
	
	const PROCESS_SUCCESS = 0;
	const PROCESS_FAIL = 1;
	
	const STEP_INPUT_PARTNER_CUSTOMER = 0;
	const STEP_INPUT_PARTNER_VENDOR = 1;
	
	abstract public function getRequiredUIInputs();
	abstract public function getUIDisplay();
	abstract public function processUIInputs($inputs, $context, $mapping_ruls);
	abstract public function getReadableStatusString($lang);
	abstract public function getPossibleProcessCodes();
	abstract public function getOutputVariableNames();
	abstract public function getInputVariableNames(); 
}
