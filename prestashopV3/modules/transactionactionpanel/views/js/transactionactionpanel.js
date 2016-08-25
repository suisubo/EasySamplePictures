/*
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
*/

//function submit_transaction_panel_inputs() {	
//	var parent_form = $(this).closest("form");
//	var inputs = $(parent_form).getElementsByTagName("input");	
//	var x = document.getElementsByName('form_name');
//}	

$(document).ready(function(){
	$(document).off('click', '.transactionactionpanel_submit').on('click', '.transactionactionpanel_submit', function(e){
		e.preventDefault();	
		var parent_form = $(e.target).closest("form");
		var inputs = $(parent_form).find("input");
		
		var post_data = 'controller=ProcessAction'
			           +'&ajax=true'
			           +'&fc=module'
			           +'&module=transactionactionpanel'
			           +'&ajax=true'
		
		$.each(inputs, function (index){
			var name = $(this).attr("name");
			var value = $(this).attr("value");
			var type = $(this).attr("type");
			
			if(type != "submit")
				post_data = post_data + '&' + name + '=' + value 
		});
		
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + 'index.php?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType: 'json',
			data: post_data,
			success: function(jsonData)
			{
				
			}});

	});
});

