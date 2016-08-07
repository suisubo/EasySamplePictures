<?php

class ServiceTypeCore extends ObjectModel
{
	public $id_vendor;
	public $price;
	public $active;
	public $id_image;

	public static $definition = array(
			'table' => 'service_type',
			'primary' => 'id_service_type',
			'fields' => array(
					'active' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
					'id_image' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			),
	);

}