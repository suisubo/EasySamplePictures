<?php

class ObtainShippingLabelHandlerCore extends AbstractHandler
{
	public function getRequiredUIInputs()
	{
		$requiredUIInputs = array(array('ui_element_type' => AbstractHandlerCore::UI_REQUEST_TEXT_INPUT, 'ui_element_name' => 'shippingLabel', 'ui_element_label' => 'Shipping Label'),
				                  array('ui_element_type' => AbstractHandlerCore::UI_REQUEST_CONFIRMATION, 'ui_element_name' => 'submitLabel', 'ui_element_label' => 'Confirm Label'),				                  				                   
		                          );
		
		return $requiredUIInputs;
	}
	
	public function getUIDisplay()
	{
		return "Please maintain your shipping label and confirm.";
	}
	
	public function processUIInputs($inputs, $context)
	{
		return AbstractHandlerCore::PROCESS_SUCCESS;
	}
	
	public function getReadableStatusString($lang)
	{
		return "Waiting for shipping label...";
	}
	
	public function getPossibleProcessCodes()
	{
		return array (AbstractHandlerCore::PROCESS_SUCCESS, AbstractHandlerCore::PROCESS_FAIL);
	}
	
	public function getOutputVariableNames()
	{
		return array ("shippingLabel");
	}
	
	public function getInputVariableNames()
	{
		return array ();
	}
}