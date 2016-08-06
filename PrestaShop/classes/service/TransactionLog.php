<?php

class ServicesCore extends ObjectModel
{
	public $id_transaction;
	public $id_step;
	public $step_start_time;
	public $step_end_time;
	public $status;
	public $input;
	
	public static $definition = array(
			'table' => 'transaction_log',
			'primary' => 'id_transaction_log',
			'fields' => array(
					'step_start_time' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
					'step_end_time' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
					'id_transaction' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'id_step' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'status' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
					'input' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			),
	);
	
}