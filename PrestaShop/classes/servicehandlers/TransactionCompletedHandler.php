<?php

class TransactionCompletedHandlerCore
{
	public function getRequiredUIInputs()
	{
		return array ();
	}
	
	public function getUIDisplay()
	{
		return "Transaction Completed¡£";
	}
	
	public function processUIInputs($inputs, $context)
	{
		return AbstractHandlerCore::PROCESS_SUCCESS;
	}
	
	public function getReadableStatusString($lang)
	{
		return "Transaction Completed.";
	}
	
	public function getPossibleProcessCodes()
	{
		return array (AbstractHandlerCore::PROCESS_SUCCESS, AbstractHandlerCore::PROCESS_FAIL);
	}
	
	public function getOutputVariableNames()
	{
		return array ();
	}
	
	public function getInputVariableNames()
	{
		return array ();
	}
}