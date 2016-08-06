<?php

class TransactionCore extends ObjectModel
{
	public $id_service_type;
	public $creation_time;
	public $current_step;
	public $status;
	public $id_service_order;
	public $finish_time;
	public $context;
		
	public static $definition = array(
			'table' => 'transaction',
			'primary' => 'id_transaction',
			'fields' => array(
					'id_service_type' =>        array('type' => self::TYPE_DATE, 'validate' => 'isUnsignedId', 'required' => true),
					'creation_time' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
					'current_step' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'status' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'finish_time' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
					'context' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			),
	);
	
}