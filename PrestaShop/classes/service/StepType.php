<?php

class StepTypeCore extends ObjectModel
{
	public $step_handler;
	
	public static $definition = array(
			'table' => 'step_type',
			'primary' => 'id_step_type',
			'fields' => array(
					'step_handler' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),					
			),
	);
	
}