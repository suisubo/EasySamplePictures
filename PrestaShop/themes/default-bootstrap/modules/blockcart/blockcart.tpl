{*
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
*}
<!-- MODULE Block cart -->
{if isset($blockcart_top) && $blockcart_top}
<div class="col-sm-4 clearfix{if $PS_CATALOG_MODE} header_user_catalog{/if}">
{/if}
	<div class="shopping_cart">
		<a href="{$link->getPageLink($order_process, true)|escape:'html':'UTF-8'}" title="{l s='View my shopping cart' mod='blockcart'}" rel="nofollow">
			<b>{l s='Cart' mod='blockcart'}</b>
			<span class="ajax_cart_quantity{if $cart_qties == 0} unvisible{/if}">{$cart_qties}</span>
			<span class="ajax_cart_product_txt{if $cart_qties != 1} unvisible{/if}">{l s='Product' mod='blockcart'}</span>
			<span class="ajax_cart_product_txt_s{if $cart_qties < 2} unvisible{/if}">{l s='Products' mod='blockcart'}</span>
			<span class="ajax_cart_total{if $cart_qties == 0} unvisible{/if}">
				{if $cart_qties > 0}
					{if $priceDisplay == 1}
						{assign var='blockcart_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
						{convertPrice price=$cart->getOrderTotal(false, $blockcart_cart_flag)}
					{else}
						{assign var='blockcart_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
						{convertPrice price=$cart->getOrderTotal(true, $blockcart_cart_flag)}
					{/if}
				{/if}
			</span>
			<span class="ajax_cart_no_product{if $cart_qties > 0} unvisible{/if}">{l s='(empty)' mod='blockcart'}</span>
			{if $ajax_allowed && isset($blockcart_top) && !$blockcart_top}
				<span class="block_cart_expand{if !isset($colapseExpandStatus) || (isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded')} unvisible{/if}">&nbsp;</span>
				<span class="block_cart_collapse{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'collapsed'} unvisible{/if}">&nbsp;</span>
			{/if}
		</a>
		{if !$PS_CATALOG_MODE}
			<div class="cart_block block exclusive">
				<div class="block_content">
					<!-- block list of products -->
					<div class="cart_block_list{if isset($blockcart_top) && !$blockcart_top}{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded' || !$ajax_allowed || !isset($colapseExpandStatus)} expanded{else} collapsed unvisible{/if}{/if}">
						{if $products}
							<dl class="products">
								{foreach from=$products item='product' name='myLoop'}
									{assign var='productId' value=$product.id_product}
									{assign var='productAttributeId' value=0}
									<dt data-id="cart_block_product_{$product.id_product|intval}_0_0" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
										<a class="cart-images" href="{$link->getProductLink($product.id_product, $product.link_rewrite)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')}" alt="{$product.name|escape:'html':'UTF-8'}" /></a>
										<div class="cart-info">
											<div class="product-name">
												<span class="quantity-formated"><span class="quantity">{$product.cart_quantity}</span>&nbsp;x&nbsp;</span><a class="cart_block_product_name" href="{$link->getProductLink($product, $product.link_rewrite, null, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name|truncate:13:'...'|escape:'html':'UTF-8'}</a>
											</div>
											<span class="price">
												{displayWtPrice p="`$product.total`"}
											</span>
										</div>
										<span class="remove_link">											
												<a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete=1&id_service_product={$product.id_product|intval}&ipa={$product.id_product_attribute|intval}&token={$static_token}")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='remove this product from my cart' mod='blockcart'}">&nbsp;</a>											
										</span>
									</dt>									
								{/foreach}
							</dl>
						{/if}
						<p class="cart_block_no_products{if $products} unvisible{/if}">
							{l s='No products' mod='blockcart'}
						</p>						
						<div class="cart-prices">							
							<div class="cart-prices-line last-line">
								<span class="price cart_block_total ajax_block_cart_total">{$total}</span>
								<span>{l s='Total' mod='blockcart'}</span>
							</div>							
						</div>
						<p class="cart-buttons">
							<a id="button_order_cart" class="btn btn-default button button-small" href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Check out' mod='blockcart'}" rel="nofollow">
								<span>
									{l s='Check out' mod='blockcart'}<i class="icon-chevron-right right"></i>
								</span>
							</a>
						</p>
					</div>
				</div>
			</div><!-- .cart_block -->
		{/if}
	</div>
{if isset($blockcart_top) && $blockcart_top}
</div>
{/if}
{counter name=active_overlay assign=active_overlay}
{if !$PS_CATALOG_MODE && $active_overlay == 1}
	<div id="layer_cart">
		<div class="clearfix">
			<div class="layer_cart_product col-xs-12 col-md-6">
				<span class="cross" title="{l s='Close window' mod='blockcart'}"></span>
				<span class="title">
					<i class="icon-check"></i>{l s='Product successfully added to your shopping cart' mod='blockcart'}
				</span>
				<div class="product-image-container layer_cart_img">
				</div>
				<div class="layer_cart_product_info">
					<span id="layer_cart_product_title" class="product-name"></span>
					<span id="layer_cart_product_attributes"></span>
					<div>
						<strong class="dark">{l s='Quantity' mod='blockcart'}</strong>
						<span id="layer_cart_product_quantity"></span>
					</div>
					<div>
						<strong class="dark">{l s='Total' mod='blockcart'}</strong>
						<span id="layer_cart_product_price"></span>
					</div>
				</div>
			</div>
			<div class="layer_cart_cart col-xs-12 col-md-6">
				<span class="title">
					<!-- Plural Case [both cases are needed because page may be updated in Javascript] -->
					<span class="ajax_cart_product_txt_s {if $cart_qties < 2} unvisible{/if}">
						{l s='There are [1]%d[/1] items in your cart.' mod='blockcart' sprintf=[$cart_qties] tags=['<span class="ajax_cart_quantity">']}
					</span>
					<!-- Singular Case [both cases are needed because page may be updated in Javascript] -->
					<span class="ajax_cart_product_txt {if $cart_qties > 1} unvisible{/if}">
						{l s='There is 1 item in your cart.' mod='blockcart'}
					</span>
				</span>
				<div class="layer_cart_row">
					<strong class="dark">
						{l s='Total products' mod='blockcart'}
						{if $use_taxes && $display_tax_label && $show_tax}
							{if $priceDisplay == 1}
								{l s='(tax excl.)' mod='blockcart'}
							{else}
								{l s='(tax incl.)' mod='blockcart'}
							{/if}
						{/if}
					</strong>
					<span class="ajax_block_products_total">
						{if $cart_qties > 0}
							{convertPrice price=$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS)}
						{/if}
					</span>
				</div>
				
				<div class="layer_cart_row">					
				</div>
				<div class="layer_cart_row">
					<strong class="dark">
						{l s='Total' mod='blockcart'}						
					</strong>
					<span class="ajax_block_cart_total">
						{if $cart_qties > 0}
							{if $priceDisplay == 1}
								{convertPrice price=$cart->getOrderTotal(false)}
							{else}
								{convertPrice price=$cart->getOrderTotal(true)}
							{/if}
						{/if}
					</span>
				</div>
				<div class="button-container">
					<span class="continue btn btn-default button exclusive-medium" title="{l s='Continue shopping' mod='blockcart'}">
						<span>
							<i class="icon-chevron-left left"></i>{l s='Continue shopping' mod='blockcart'}
						</span>
					</span>
					<a class="btn btn-default button button-medium"	href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Proceed to checkout' mod='blockcart'}" rel="nofollow">
						<span>
							{l s='Proceed to checkout' mod='blockcart'}<i class="icon-chevron-right right"></i>
						</span>
					</a>
				</div>
			</div>
		</div>
		<div class="crossseling"></div>
	</div> <!-- #layer_cart -->
	<div class="layer_cart_overlay"></div>
{/if}
{strip}

{addJsDef img_dir=$img_dir|escape:'quotes':'UTF-8'}
{addJsDef generated_date=$smarty.now|intval}
{addJsDef ajax_allowed=$ajax_allowed|boolval}
{addJsDef hasDeliveryAddress=(isset($cart->id_address_delivery) && $cart->id_address_delivery)}

{addJsDefL name=customizationIdMessage}{l s='Customization #' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=removingLinkText}{l s='remove this product from my cart' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=freeShippingTranslation}{l s='Free shipping!' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=freeProductTranslation}{l s='Free!' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=delete_txt}{l s='Delete' mod='blockcart' js=1}{/addJsDefL}
{addJsDefL name=toBeDetermined}{l s='To be determined' mod='blockcart' js=1}{/addJsDefL}
{/strip}
<!-- /MODULE Block cart -->
