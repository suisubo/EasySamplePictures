<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CartControllerCore extends FrontController
{
    public $php_self = 'cart';

    protected $id_service_product;
    protected $qty;
    public $ssl = true;

    protected $ajax_refresh = false;

    /**
     * This is not a public page, so the canonical redirection is disabled
     *
     * @param string $canonicalURL
     */
    public function canonicalRedirection($canonicalURL = '')
    {
    }

    /**
     * Initialize cart controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        // Send noindex to avoid ghost carts by bots
        header('X-Robots-Tag: noindex, nofollow', true);

        // Get page main parameters
        $this->id_service_product = (int)Tools::getValue('id_service_product', null);        
        $this->qty = abs(Tools::getValue('qty', 1));        
    }

    public function postProcess()
    {
        // Update the cart ONLY if $this->cookies are available, in order to avoid ghost carts created by bots
        if ($this->context->cookie->exists() && !$this->errors && !($this->context->customer->isLogged() && !$this->isTokenValid())) {
            if (Tools::getIsset('add') || Tools::getIsset('update')) {
                $this->processChangeProductInCart();
            } elseif (Tools::getIsset('delete')) {
                $this->processDeleteProductInCart();
            } 
            
            // Make redirection
            if (!$this->errors && !$this->ajax) {
                $queryString = Tools::safeOutput(Tools::getValue('query', null));
                if ($queryString && !Configuration::get('PS_CART_REDIRECT')) {
                    Tools::redirect('index.php?controller=search&search='.$queryString);
                }

                // Redirect to previous page
                if (isset($_SERVER['HTTP_REFERER'])) {
                    preg_match('!http(s?)://(.*)/(.*)!', $_SERVER['HTTP_REFERER'], $regs);
                    if (isset($regs[3]) && !Configuration::get('PS_CART_REDIRECT')) {
                        $url = preg_replace('/(\?)+content_only=1/', '', $_SERVER['HTTP_REFERER']);
                        Tools::redirect($url);
                    }
                }

                Tools::redirect('index.php?controller=order&'.(isset($this->id_product) ? 'ipa='.$this->id_product : ''));
            }
        } elseif (!$this->isTokenValid()) {
            Tools::redirect('index.php');
        }
    }

    /**
     * This process delete a product from the cart
     */
    protected function processDeleteProductInCart()
    {
        if ($this->context->cart->deleteProduct($this->id_service_product)) {
            if (!Cart::getNbProducts((int)$this->context->cart->id)) {
                $this->context->cart->setDeliveryOption(null);
                $this->context->cart->gift = 0;
                $this->context->cart->gift_message = '';
                $this->context->cart->update();
            }
        }
//         $removed = CartRule::autoRemoveFromCart();
//         CartRule::autoAddToCart();
//         if (count($removed) && (int)Tools::getValue('allow_refresh')) {
//             $this->ajax_refresh = true;
//         }
    }
    
    /**
     * This process add or update a product in the cart
     */
    protected function processChangeProductInCart()
    {
        $mode = (Tools::getIsset('update') && $this->id_service_product) ? 'update' : 'add';

        if ($this->qty == 0) {
            $this->errors[] = Tools::displayError('Null quantity.', !Tools::getValue('ajax'));
        } elseif (!$this->id_service_product) {
            $this->errors[] = Tools::displayError('Product not found', !Tools::getValue('ajax'));
        }

        $serviceproduct = new ServiceProduct($this->id_service_product, true, $this->context->language->id);
        
        // If no errors, process product addition
        if (!$this->errors && $mode == 'add') {
            // Add cart if no cart found
            if (!$this->context->cart->id) {
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
            }           

            if (!$this->errors) {
                //$cart_rules = $this->context->cart->getCartRules();
                //$available_cart_rules = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
                $update_quantity = $this->context->cart->updateQty($this->qty, $this->id_service_product);
//                 if ($update_quantity < 0) {
//                     // If product has attribute, minimal quantity is set with minimal quantity of attribute
//                     $minimal_quantity = 1;
//                     $this->errors[] = sprintf(Tools::displayError('You must add %d minimum quantity', !Tools::getValue('ajax')), $minimal_quantity);
//                 } elseif (!$update_quantity) {
//                     $this->errors[] = Tools::displayError('You already have the maximum quantity available for this product.', !Tools::getValue('ajax'));
//                 } elseif ((int)Tools::getValue('allow_refresh')) {
//                     // If the cart rules has changed, we need to refresh the whole cart
//                     $cart_rules2 = $this->context->cart->getCartRules();
//                     if (count($cart_rules2) != count($cart_rules)) {
//                         $this->ajax_refresh = true;
//                     } elseif (count($cart_rules2)) {
//                         $rule_list = array();
//                         foreach ($cart_rules2 as $rule) {
//                             $rule_list[] = $rule['id_cart_rule'];
//                         }
//                         foreach ($cart_rules as $rule) {
//                             if (!in_array($rule['id_cart_rule'], $rule_list)) {
//                                 $this->ajax_refresh = true;
//                                 break;
//                             }
//                         }
//                     } else {
//                         $available_cart_rules2 = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
//                         if (count($available_cart_rules2) != count($available_cart_rules)) {
//                             $this->ajax_refresh = true;
//                         } elseif (count($available_cart_rules2)) {
//                             $rule_list = array();
//                             foreach ($available_cart_rules2 as $rule) {
//                                 $rule_list[] = $rule['id_cart_rule'];
//                             }
//                             foreach ($cart_rules2 as $rule) {
//                                 if (!in_array($rule['id_cart_rule'], $rule_list)) {
//                                     $this->ajax_refresh = true;
//                                     break;
//                                 }
//                             }
//                         }
//                     }
//                 }
            }
        }

        //$removed = CartRule::autoRemoveFromCart();
        //CartRule::autoAddToCart();
        //if (count($removed) && (int)Tools::getValue('allow_refresh')) {
        //    $this->ajax_refresh = true;
        //}
        
        //$this->ajax_refresh = true;
    }

    /**
     * Remove discounts on cart
     *
     * @deprecated 1.5.3.0
     */
    protected function processRemoveDiscounts()
    {
        Tools::displayAsDeprecated();
        $this->errors = array_merge($this->errors, CartRule::autoRemoveFromCart());
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->setTemplate(_PS_THEME_DIR_.'errors.tpl');
        if (!$this->ajax) {
            parent::initContent();
        }
    }

    /**
     * Display ajax content (this function is called instead of classic display, in ajax mode)
     */
    public function displayAjax()
    {
        if ($this->errors) {
            $this->ajaxDie(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
        }
        if ($this->ajax_refresh) {
            $this->ajaxDie(Tools::jsonEncode(array('refresh' => true)));
        }

        // write cookie if can't on destruct
        $this->context->cookie->write();

        if (Tools::getIsset('summary')) {
            $result = array();
            
            $result['summary'] = $this->context->cart->getSummaryDetails(null, true);
            $json = "";
            $this->ajaxDie(Tools::jsonEncode(array_merge($result, (array)Tools::jsonDecode($json, true))));
        }
        // @todo create a hook
        elseif (file_exists(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php')) {
            //require_once(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php');
        	$context = Context::getContext();
        	$blockCart = Module::getInstanceByName('blockcart');
        	echo $blockCart->hookAjaxCall(array('cookie' => $context->cookie, 'cart' => $context->cart));
        }
    }
}
