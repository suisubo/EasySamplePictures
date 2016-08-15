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

{*************************************************************************************************************************************}
{* IMPORTANT : If you change some data here, you have to report these changes in the ./blockcart-json.js (to let ajaxCart available) *}
{*************************************************************************************************************************************}
{if $ajax_allowed}
<script type="text/javascript">
var img_dir = '{$img_dir|addslashes}';
</script>
{/if}
<script type="text/javascript">
var customizationIdMessage = '{l s='Customization #' mod='blockcart' js=1}';
var removingLinkText = '{l s='remove this product from my cart' mod='blockcart' js=1}';
var freeShippingTranslation = '{l s='Free shipping!' mod='blockcart' js=1}';
var freeProductTranslation = '{l s='Free!' mod='blockcart' js=1}';
var delete_txt = '{l s='Delete' mod='blockcart' js=1}';
var generated_date = {$smarty.now|intval};
</script>


<!-- MODULE Block cart -->
<div id="cart_block" class="block exclusive">
	<h4 class="title_block">
		<a href="{$link->getPageLink("$order_process", true)|escape:'html'}" title="{l s='View my shopping cart' mod='blockcart'}" rel="nofollow">{l s='Cart' mod='blockcart'}</a>
		{if $ajax_allowed}
		<span id="block_cart_expand" {if isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded' || !isset($colapseExpandStatus)}class="hidden"{/if}>&nbsp;</span>
		<span id="block_cart_collapse" {if isset($colapseExpandStatus) && $colapseExpandStatus eq 'collapsed'}class="hidden"{/if}>&nbsp;</span>
		{/if}
	</h4>
	<div class="block_content">
	<!-- block summary -->
	<div id="cart_block_summary" class="{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded' || !$ajax_allowed || !isset($colapseExpandStatus)}collapsed{else}expanded{/if}">
		<span class="ajax_cart_quantity" {if $cart_qties <= 0}style="display:none;"{/if}>{$cart_qties}</span>
		<span class="ajax_cart_product_txt_s" {if $cart_qties <= 1}style="display:none"{/if}>{l s='Products' mod='blockcart'}</span>
		<span class="ajax_cart_product_txt" {if $cart_qties > 1}style="display:none"{/if}>{l s='Product' mod='blockcart'}</span>
		<span class="ajax_cart_total" {if $cart_qties == 0}style="display:none"{/if}>
			{if $cart_qties > 0}
				{if $priceDisplay == 1}
					{convertPrice price=$cart->getOrderTotal(false)}
				{else}
					{convertPrice price=$cart->getOrderTotal(true)}
				{/if}
			{/if}
		</span>
		<span class="ajax_cart_no_product" {if $cart_qties != 0}style="display:none"{/if}>{l s='(empty)' mod='blockcart'}</span>
	</div>
	<!-- block list of products -->
	<div id="cart_block_list" class="{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded' || !$ajax_allowed || !isset($colapseExpandStatus)}expanded{else}collapsed{/if}">
	{if $products}
		<dl class="products">
		{foreach from=$products item='product' name='myLoop'}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			<dt id="cart_block_product_{$product.id_product}_0_0" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
				<span class="quantity-formated"><span class="quantity">{$product.cart_quantity}</span>x</span>
				<a class="cart_block_product_name" href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
				{$product.name|truncate:13:'...'|escape:html:'UTF-8'}</a>
				<span class="remove_link"><a rel="nofollow" class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product}&amp;token={$static_token}", true)|escape:'html'}" title="{l s='Please remove this product from my cart.' mod='blockcart'}">&nbsp;</a></span>
				<span class="price">
					{displayWtPrice p="`$product.total`"}
				</span>
			</dt>
		{/foreach}
		</dl>
	{/if}
		<p {if $products}class="hidden"{/if} id="cart_block_no_products">{l s='No products' mod='blockcart'}</p>
		
		<p id="cart-prices">			
			<span id="cart_block_total" class="price ajax_block_cart_total">{$total}</span>
			<span>{l s='Total' mod='blockcart'}</span>
		</p>		
		<p id="cart-buttons">
			{if $order_process == 'order'}<a href="{$link->getPageLink("$order_process", true)|escape:'html'}" class="button_small" title="{l s='View my shopping cart' mod='blockcart'}" rel="nofollow">{l s='Cart' mod='blockcart'}</a>{/if}
			<a href="{$link->getPageLink("$order_process", true)|escape:'html'}" id="button_order_cart" class="exclusive{if $order_process == 'order-opc'}_large{/if}" title="{l s='Check out' mod='blockcart'}" rel="nofollow"><span></span>{l s='Check out' mod='blockcart'}</a>
		</p>
	</div>
	</div>
</div>
<!-- /MODULE Block cart -->

