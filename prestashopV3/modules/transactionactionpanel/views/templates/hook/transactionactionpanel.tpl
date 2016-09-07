{* * 2007-2015 PrestaShop * * NOTICE OF LICENSE * * This source file is
subject to the Academic Free License (AFL 3.0) * that is bundled with
this package in the file LICENSE.txt. * It is also available through the
world-wide-web at this URL: * http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to *
obtain it through the world-wide-web, please send an email * to
license@prestashop.com so we can send you a copy immediately. * *
DISCLAIMER * * Do not edit or add to this file if you wish to upgrade
PrestaShop to serviceer * versions in the future. If you wish to
customize PrestaShop for your * needs please refer to
http://www.prestashop.com for more information. * * @author PrestaShop
SA <contact@prestashop.com> * @copyright 2007-2015 PrestaShop SA *
@license http://opensource.org/licenses/afl-3.0.php Academic Free
License (AFL 3.0) * International Registered Trademark & Property of
PrestaShop SA *}

<!-- MODULE Block service products -->


<div id="transation_block_{$transaction['id_transaction']}"
	class="transation_block">
	<h2 class="transaction_block">{if $transaction.show_product_name }{$transaction['product_name']}{/if}</h2>

	<div class="transaction_action_panel" enctype="multipart/form-data">
		<!-- progressbar -->
		<ul id="progressbar">
			{foreach from=$transaction.service_steps item=service_step
			name=service_steps}
			<li
				class="{($transaction['current_step'] == $service_step['id_step'])?'active':''}">{if $service_step.action_partner == $transaction['is_admin']} {$service_step['name_action']} {else} {$service_step['name']} {/if}</li>
			{/foreach}
		</ul>

		<fieldset id="transation_form_{$transaction['id_transaction']}"
			action="#" method="POST">
			<div id="error_{$transaction['id_transaction']}" class="transaction_error"></div>
			<div class="transaction_container">
			    <div class='transaction_right'>
			        <b>Step Description:</b> <br>
					{$transaction['description']} <br>
					<br> <br> <br>
					{if $transaction.show_instruction && $transaction.instruction != null}
					<b>Instruction:</b> <br>  
					{$transaction['instruction']} <br>
					{/if}
				</div>
				<div class="transaction_left">
				{foreach from=$transaction.ui_list item=ui_item name=ui_list} {if
					$ui_item.ui_element_type == "form"}
					{$ui_item.ui_element_form} {/if} {/foreach} 
				<form id="msform" enctype="multipart/form-data">
				    {if isset($transaction['status_string'])}
				    <b>Current Status:</b> <br> 
				    {$transaction['status_string']} <br>
					{/if}
					<input type="hidden" name="transaction_id"
						value="{$transaction['id_transaction']}"><input type="hidden" name="base_url"
						value="{$transaction['base_url']}"> <input type="hidden"
						name="current_step" value="{$transaction['current_step']}"> 
						<input type="hidden"
						name="is_admin" value="{$transaction['is_admin']}"><input
						type="hidden" name="stephandler"
						value="{$transaction['stephandler']}"> <input type="hidden"
						name="steptype" value="{$transaction['steptype']}"> <input
						type="hidden" name="servicetype"
						value="{$transaction['servicetype']}"> <input type="hidden"
						name="id_product" value="{$transaction['id_product']}"> <input
						type="hidden" name="ajax" value="true"> <input type="hidden"
						name="fc" value="module"> <input type="hidden" name="module"
						value="transactionactionpanel"> <input type="hidden"
						name="controller" value="{$transaction['controller']}">
						<input type="hidden"
						name="token" value="{$transaction['token']}">
						<br> {if
					isset($transaction.ui_list)}{foreach from=$transaction.ui_list
					item=ui_item name=ui_list} {if $ui_item.ui_element_type == "radio"}
					<input type="radio" name="{$ui_item.ui_element_name}"
						value="{$ui_item.ui_element_value}"> {$ui_item.ui_element_label}<br> 
                    {/if} {/foreach}
					{foreach from=$transaction.ui_list
					item=ui_item name=ui_list} {if $ui_item.ui_element_type == "text"}
					<input type="text" name="{$ui_item.ui_element_name}"
						placeholder="{$ui_item.ui_element_label}"> {/if} {/foreach}
					{foreach from=$transaction.ui_list item=ui_item name=ui_list} {if
					$ui_item.ui_element_type == "custom"}
					{$ui_item.ui_element_custom_content} {/if} {/foreach} {foreach
					from=$transaction.ui_list item=ui_item name=ui_list} {if
					$ui_item.ui_element_type == "file"} <label
						for="{$ui_item.ui_element_name}"> {$ui_item.ui_element_label} </label>
					<input type="file" name="{$ui_item.ui_element_name}[]" multiple {if
						isset($ui_item.ui_element_accept)} id="{$ui_item.ui_element_name}"
						accpet="{$ui_item.ui_element_accept}"{/if}> {/if} {/foreach}
					{if $transaction.show_submit_button}
					{foreach from=$transaction.ui_list item=ui_item name=ui_list} {if
					$ui_item.ui_element_type == "submit"} <input
						onclick="submit_transaction_panel_inputs()"
						class="transactionactionpanel_submit action-button"
						id="transation_submit_{$transaction['id_transaction']}"
						type="button" name="{$ui_item.ui_element_name}"
						value="{$ui_item.ui_element_label}"> {/if} {/foreach}{/if}{/if}
					<div class='transaction_demo_nav'>				
				    {foreach from=$transaction.extended_buttons item=extended_button name=extended_buttons} {if
					$extended_button.ui_element_type == "submit"} <input
						onclick="submit_transaction_panel_inputs()"
						class="transactionactionpanel_nav action-button"
						id = "{$extended_button.ui_element_name}"
						type="button" name="{$extended_button.ui_element_name}"
						value="{$extended_button.ui_element_label}"> {/if} {/foreach}
				     </div>	
				</form>     	
				</div>
							
			</div>
			<div id="transaction_footnote_{$transaction['id_transaction']}"
				, class="transaction_foot_note">
				{foreach from=$transaction.public_params item=public_param
				name=public_params}
				{$public_param.displayName}:{$public_param.value}<br> {/foreach}
			</div>
		</fieldset>
	</div>
</div>



<!-- 	<div class="block_content"> -->
<!-- 	{if $service_products !== false} -->
<!-- 		<ul class="product_images clearfix"> -->
<!-- 		{foreach from=$service_products item='product' name='serviceProducts'} -->
<!-- 			{if $smarty.foreach.serviceProducts.index < 2} -->
<!-- 				<li{if $smarty.foreach.serviceProducts.first} class="first"{/if}><a href="{$product.link|escape:'html'}" title="{$product.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_default')|escape:'html'}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$product.legend|escape:html:'UTF-8'}" /></a></li> -->
<!-- 			{/if} -->
<!-- 		{/foreach} -->
<!-- 		</ul> -->
<!-- 		<dl class="products"> -->
<!-- 		{foreach from=$service_products item=serviceproduct name=myLoop} -->
<!-- 			<dt class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}"><a href="{$serviceproduct.link|escape:'html'}" title="{$serviceproduct.name|escape:html:'UTF-8'}">{$serviceproduct.name|strip_tags|escape:html:'UTF-8'}</a></dt> -->
<!-- 			{if $serviceproduct.description_short}<dd class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}"><a href="{$serviceproduct.link|escape:'html'}">{$serviceproduct.description_short|strip_tags:'UTF-8'|truncate:75:'...'}</a><br /><a href="{$serviceproduct.link}" class="lnk_more">{l s='Read more' mod='blockserviceproducts'}</a></dd>{/if} -->
<!-- 		{/foreach} -->
<!-- 		</dl> -->
<!-- 		<p><a href="{$link->getPageLink('service-products')|escape:'html'}" title="{l s='All service products' mod='blockserviceproducts'}" class="button_large">&raquo; {l s='All service products' mod='blockserviceproducts'}</a></p> -->
<!-- 	{else} -->
<!-- 		<p>&raquo; {l s='Do not allow service products at this time.' mod='blockserviceproducts'}</p> -->
<!-- 	{/if} -->
<!-- 	</div> -->

<!-- /MODULE Block service products -->
