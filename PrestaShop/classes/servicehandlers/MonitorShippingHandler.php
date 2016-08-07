<?php

class MonitorShippingHandlerCore extends AbstractHandler
{
	public function getRequiredUIInputs()
	{
		$requiredUIInputs = array(array('ui_element_type' => AbstractHandlerCore::UI_REQUEST_CONFIRMATION, 'ui_element_name' => 'submitConfirmShipping', 'ui_element_label' => 'Comfirm Shipping Received'),
		);
	
		return $requiredUIInputs;
	}
	
	public function getUIDisplay()
	{
		return "Shipping Status Tracking.";
	}
	
	public function processUIInputs($inputs, $context)
	{
		return AbstractHandlerCore::PROCESS_SUCCESS;
	}
	
	public function getReadableStatusString($lang)
	{
		return "Shipping Status Tracking...";
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
