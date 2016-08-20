<?php

class ServicesCore extends ObjectModel
{
	public $creation_time;
	public $finish_time;
	public $id_customer;
	public $id_shop;
	public $id_cart;
	public $payment;
	public $total_paid;
	public $id_currency;
	
	public static $definition = array(
			'table' => 'z_service_orders',
			'primary' => 'id_service_order',
			'fields' => array(
					'creation_time' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
					'finish_time' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
					'id_customer' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'id_cart' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
					'payment' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
					'total_paid' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
					'id_currency' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			),
	);
	
}