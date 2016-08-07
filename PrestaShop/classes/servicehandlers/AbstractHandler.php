<?php

abstract class AbstractHandlerCore
{
	const UI_REQUEST_CONFIRMATION = 1;
	const UI_REQUEST_TEXT_INPUT = 2;
	const UI_REQUEST_FILE_UPLOAD = 3;
	const UI_REQUEST_FILE_DOWNLOAD = 4;
	
	abstract public function getRequiredUIInputs();
	abstract public function getDisplayMessage();
	abstract public function processUIInputs($inputs, $context, $mapping_ruls);
	abstract public function getReadableStatusString($lang);
	abstract public function getPossibleExitCodes();
	abstract public function getOutputVariableNames();
	abstract public function getInputVariableNames();
}
