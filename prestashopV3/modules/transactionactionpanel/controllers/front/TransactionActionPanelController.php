<?php

if (!defined('_PS_VERSION_'))
	exit;

class TransactionActionPanelController extends ModuleFrontController
{
	public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}
	
	public function init()
	{
		parent::init();
		$this->display_column_left = false;
		$this->display_column_right = false;
	}
	
	public function setMedia()
	{
		parent::setMedia();
	}
	
	public function initContent()
	{
		parent::initContent();		
	}
	
}