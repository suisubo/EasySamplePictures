<?php

class OffersHandlerCore extends AbstractHandler
{
	public function getReadableStatusString($context_inputs, $service_parameters, $lang = null){
		return null;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
	
	
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		$offer_amount = Tools::getValue('accpted_offer');
		$outputs['accepted_offer_amount'] = $offer_amount;
		return AbstractHandler::PROCESS_SUCCESS;
	}
	
	public function getAdditionalInputUIElements($context_inputs, $service_parameters){
		foreach($context_inputs as $name => $value)
		{
			if (preg_match ('/offer_(\d+)/', $name, $maches )) 
			{				
				$input['ui_element_type'] = 'radio';
				$input['ui_element_name'] = 'accpted_offer';
				$input['ui_element_value'] = $value;
				$input['ui_element_label'] = $value;				
				$ui_list[] = $input;
			}
		}
		
		return $ui_list;
	}
	
	public function processUIInputsNonAction($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		$actionbutton = Tools::getValue('actionbutton');
		if($actionbutton == 'transaction_offer')
		{
			if(isset(Context::getContext()->employee))
			{
				$id_employee = Context::getContext()->employee->id;
				$outputs['offer_'.$id_employee] = Tools::getValue('my_offer');
				$outputs['offer_text_'.$id_employee] = Tools::getValue('my_comment');
			}
			
		}
	}
	
	public function getAdditionalInputUIElementsNonAction($context_inputs, $service_parameters)
	{
		if(isset(Context::getContext()->employee))
		{
			$id_employee = Context::getContext()->employee->id;
			
			if(isset($context_inputs['offer_'.$id_employee]))
			{
				$ui = '<label><font size="4">您当前的出价为人民币 '.$context_inputs['offer_'.$id_employee].' 元</font></label><br<br>';
			}

			$ui = $ui.'<input type="number" step="any" placeholder="我的出价 (人民币)" name="my_offer"/><br>';
			$ui = $ui.'<input type="text" placeholder="附言" name="my_comment"/>';
			
			$modify = '提交';
			if(isset($context_inputs['offer_'.$id_employee]))
				$modify = '修改';
			
			$ui = $ui.'<input type="button" class="action-button transaction_nonaction" name="transaction_offer" value="'.$modify.'我的出价"/>';
				
			$input_offer['ui_element_type'] = 'custom';
			$input_offer['ui_element_custom_content'] = $ui;
				
			$ui_list[] = $input_offer;
				
			return $ui_list;
		}else{
			return null;
		}
	}
}