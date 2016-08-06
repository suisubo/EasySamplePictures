<?php

class ServiceTypeCore extends ObjectModel
{
	public $step_handler;
	public $step_partner_type;
	
	public static $definition = array(
			'table' => 'service_type',
			'primary' => 'id_step_type',
			'fields' => array(
					'step_handler' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
					'step_partner_type' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),					
			),
	);
	
}