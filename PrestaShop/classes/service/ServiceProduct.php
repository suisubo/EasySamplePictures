<?php

class ServiceProductCore extends ObjectModel
{
	public $param_name;
	public $param_value;

	public static $definition = array(
			'table' => 'z_service_product',
			'primary' => 'id_service_product',
			'fields' => array(
					'product_name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
					'price' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
					'id_service_type' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			),
	);

}