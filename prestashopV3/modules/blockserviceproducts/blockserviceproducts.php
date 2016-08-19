<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockServiceProducts extends Module
{
	protected static $cache_service_products;

	public function __construct()
	{
		$this->name = 'blockserviceproducts';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'SuperInnovation LLC';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Service products block');
		$this->description = $this->l('Displays a block featuring your store\'s service products.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		$success = (parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('leftColumn')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
			&& $this->registerHook('displayHomeTab')
			&& $this->registerHook('displayHomeTabContent')
		);

		$this->_clearCache('*');

		return $success;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBlockServiceProducts'))
		{
			Configuration::updateValue('PS_BLOCK_SERVICEPRODUCTS_DISPLAY', (int)(Tools::getValue('PS_BLOCK_SERVICEPRODUCTS_DISPLAY')));
			$this->_clearCache('*');
			$output .= $this->displayConfirmation($this->l('Settings updated'));
			
		}
		return $output.$this->renderForm();
	}

	protected function getServiceProducts()
	{
		$serviceProducts = false;
		$serviceProducts = Product::getProducts((int) $this->context->language->id);

		if (!$serviceProducts && Configuration::get('PS_BLOCK_SERVICEPRODUCTS_DISPLAY'))
			return;
		return $serviceProducts;
	}

	public function hookRightColumn($params)
	{
		if (!$this->isCached('blockserviceproducts.tpl', $this->getCacheId()))
		{
			if (!isset(BlockServiceProducts::$cache_service_products))
				BlockServiceProducts::$cache_service_products = $this->getServiceProducts();

			$this->smarty->assign(array(
				'service_products' => BlockServiceProducts::$cache_service_products,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
		}

		if (BlockServiceProducts::$cache_service_products === false)
			return false;

		return $this->display(__FILE__, 'blockserviceproducts.tpl', $this->getCacheId());
	}

	protected function getCacheId($name = null)
	{
		if ($name === null)
			$name = 'blockserviceproducts';
		return parent::getCacheId($name.'|'.date('Ymd'));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookdisplayHomeTab($params)
	{
		if (!$this->isCached('tab.tpl', $this->getCacheId('blockserviceproducts-tab')))
			BlockServiceProducts::$cache_service_products = $this->getServiceProducts();

		if (BlockServiceProducts::$cache_service_products === false)
			return false;

		return $this->display(__FILE__, 'tab.tpl', $this->getCacheId('blockserviceproducts-tab'));
	}

	public function hookdisplayHomeTabContent($params)
	{
		if (!$this->isCached('blockserviceproducts_home.tpl', $this->getCacheId('blockserviceproducts-home')))
		{
			$this->smarty->assign(array(
				'service_products' => BlockServiceProducts::$cache_service_products,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
		}

		if (BlockServiceProducts::$cache_service_products === false)
			return false;

		return $this->display(__FILE__, 'blockserviceproducts_home.tpl', $this->getCacheId('blockserviceproducts-home'));
	}

	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index')
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');

		$this->context->controller->addCSS($this->_path.'blockserviceproducts.css', 'all');
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('*');
	}

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('blockserviceproducts.tpl');
		parent::_clearCache('blockserviceproducts_home.tpl', 'blockserviceproducts-home');
		parent::_clearCache('tab.tpl', 'blockserviceproducts-tab');
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Always display this block'),
						'name' => 'PS_BLOCK_SERVICEPRODUCTS_DISPLAY',
						'desc' => $this->l('Show the block even if no service products are available.'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockServiceProducts';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'PS_BLOCK_SERVICEPRODUCTS_DISPLAY' => Tools::getValue('PS_BLOCK_SERVICEPRODUCTS_DISPLAY', Configuration::get('PS_BLOCK_SERVICEPRODUCTS_DISPLAY')),			
		);
	}
}
