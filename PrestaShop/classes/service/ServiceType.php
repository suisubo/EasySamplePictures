<?php

class ServicesCore extends ObjectModel
{
	public $step_handler;
	public $step_partner_type;
	
	public static $definition = array(
			'table' => 'step_type',
			'primary' => 'id_step_type',
			'fields' => array(
					'price' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
					'active' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
					'id_image' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),					
			),
	);
	
}