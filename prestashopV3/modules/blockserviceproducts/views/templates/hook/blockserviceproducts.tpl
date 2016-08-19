{*
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
* Do not edit or add to this file if you wish to upgrade PrestaShop to serviceer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- MODULE Block service products -->
<div id="service-products_block_right" class="block products_block">
	<h4 class="title_block"><a href="{$link->getPageLink('service-products')|escape:'html'}" title="{l s='Service products' mod='blockserviceproducts'}">{l s='Service products' mod='blockserviceproducts'}</a></h4>
	<div class="block_content">
	{if $service_products !== false}
		<ul class="product_images clearfix">
		{foreach from=$service_products item='product' name='serviceProducts'}
			{if $smarty.foreach.serviceProducts.index < 2}
				<li{if $smarty.foreach.serviceProducts.first} class="first"{/if}><a href="{$product.link|escape:'html'}" title="{$product.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_default')|escape:'html'}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$product.legend|escape:html:'UTF-8'}" /></a></li>
			{/if}
		{/foreach}
		</ul>
		<dl class="products">
		{foreach from=$service_products item=serviceproduct name=myLoop}
			<dt class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}"><a href="{$serviceproduct.link|escape:'html'}" title="{$serviceproduct.name|escape:html:'UTF-8'}">{$serviceproduct.name|strip_tags|escape:html:'UTF-8'}</a></dt>
			{if $serviceproduct.description_short}<dd class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}"><a href="{$serviceproduct.link|escape:'html'}">{$serviceproduct.description_short|strip_tags:'UTF-8'|truncate:75:'...'}</a><br /><a href="{$serviceproduct.link}" class="lnk_more">{l s='Read more' mod='blockserviceproducts'}</a></dd>{/if}
		{/foreach}
		</dl>
		<p><a href="{$link->getPageLink('service-products')|escape:'html'}" title="{l s='All service products' mod='blockserviceproducts'}" class="button_large">&raquo; {l s='All service products' mod='blockserviceproducts'}</a></p>
	{else}
		<p>&raquo; {l s='Do not allow service products at this time.' mod='blockserviceproducts'}</p>
	{/if}
	</div>
</div>
<!-- /MODULE Block service products -->
