<?php

class MonitorShippingHandlerCore extends AbstractHandler
{
	public function processUIInputs($context_inputs, &$outputs, &$error_info){    		
    }
    
	public function getReadableStatusString($context_inputs, $lang = null){
		return 'Package is on the route';
	}
}
