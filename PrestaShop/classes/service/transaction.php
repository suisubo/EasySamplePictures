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
			'primary' => 'id_service_order',
			'fields' => array(
					'creation_time' =>        array('type' => self::TYPE_DATE, 'validate' => 'isUnsignedId', 'required' => true),
					'finish_time' =>        array('type' => self::TYPE_DATE, 'validate' => 'isUnsignedId', 'required' => true),
					'id_customer' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'id_cart' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
					'payment' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
					'total_paid' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
					'id_currency' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			),
	);
	
}