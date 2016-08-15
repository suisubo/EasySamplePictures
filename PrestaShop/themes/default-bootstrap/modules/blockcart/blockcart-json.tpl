{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{ldelim}
"products": [
{if $products}
{foreach from=$products item=product name='products'}
{assign var='productId' value=$product.id_product}
{assign var='productAttributeId' value=$product.id_product_attribute}
	{ldelim}
		"id": {$product.id_product|intval},
		"link": {$link->getProductLink($product.id_product, $product.link_rewrite, null, null, null, $product.id_shop, $product.id_product_attribute)|json_encode},
		"quantity": {$product.cart_quantity|intval},
		"image": {$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|json_encode},
		"image_cart": {$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')|json_encode},
		"priceByLine": {displayWtPrice|json_encode p=$product.total},
		"name": {$product.name|trim|html_entity_decode:2:'UTF-8'|json_encode},
		"price": {displayWtPrice|json_encode p=$product.total},
		"idCombination": 0,
		"price_float": {$product.total|floatval|json_encode}		
	{rdelim}{if !$smarty.foreach.products.last},{/if}
{/foreach}{/if}
],
"nbTotalProducts": {$nb_total_products|intval},
"total": {$total|json_encode},
"productTotal": {$product_total|json_encode},
{if isset($errors) && $errors}
"hasError" : true,
"errors" : [
{foreach from=$errors key=k item=error name='errors'}
	{$error|json_encode}
	{if !$smarty.foreach.errors.last},{/if}
{/foreach}
]
{else}
"hasError" : false
{/if}
{rdelim}
