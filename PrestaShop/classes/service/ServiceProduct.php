<?php

class ServiceProductCore extends ObjectModel
{
	public $param_name;
	public $param_value;

	public static $definition = array(
			'table' => 'z_service_product',
			'primary' => 'id_service_product',
			'fields' => array(
					'name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
					'price' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
					'id_service_type' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'id_image' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
					'link_rewrite' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			),
	);
	
	public static function getServiceProducts()
	{
		$context = Context::getContext();
		
		$sql = 'SELECT p.* FROM `'._DB_PREFIX_.'z_service_product` p';
		$rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);		
		
		$results_array = array();
		
		foreach ($rq as $row) {
			$row['link'] = $context->link->getProductLink((int)$row['id_service_product'], $row['link_rewrite']);
			$results_array[] = $row;			
		}
		
		return $results_array;
	}

}