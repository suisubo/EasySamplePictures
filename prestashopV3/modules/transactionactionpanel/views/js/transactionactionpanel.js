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
$(document).ready(
		function() {
			$(document).off('click', '.transaction_nonaction').on('click',
					'.transaction_nonaction', function(e) {
				e.preventDefault();	
				var parent_form = $(e.target).closest("form");
				var inputs = $(parent_form).find("input");
				
				var formData = new FormData($(e.target).closest("form")[0]);
				formData.append('actionbutton', $(e.target).attr("name"));
								
				var base_url = '';
				var transaction_id = '';
				var terminate = 0;
				
				$.each(inputs, function (index){
					var name = $(this).attr("name");
					var value = $(this).attr("value");
					var type = $(this).attr("type");
					
					if(name == "base_url")
						base_url = value;
					
					if(name == "transaction_id")
						transaction_id = value;
					
					if(name == "current_step")
						terminate = 1;//demo mode
				});
				
				if(terminate == 1)
					return;
				
				$.ajax({
					type: 'POST',
					headers: { "cache-control": "no-cache" },
					url: base_url + 'index.php?rand=' + new Date().getTime(),
					async: true,
					cache: false,
					dataType: 'json',
					data: formData,
					contentType: false,
					processData: false,
					success: function(data)
					{
						if(!data.errors)
						{
							$("#transation_block_" + transaction_id).fadeOut("slow", function(){
								var newcontent = $(data.next_step).hide();
								$(this).replaceWith(newcontent);
								$('.fotorama').fotorama();
								$("#transation_block_" + transaction_id).fadeIn("slow");
							});
						}
					}});

			})
		});


$(document).ready(
		function() {
			$(document).off('click', '.transactionactionpanel_nav').on('click',
					'.transactionactionpanel_nav', function(e) {
				e.preventDefault();	
				var parent_form = $(e.target).closest("form");
				var inputs = $(parent_form).find("input");
				
				var formData = new FormData($(e.target).closest("form")[0]);
				formData.append('action', 'Demo');
				formData.append('actionbutton', $(e.target).attr("name"));
								
				var base_url = '';
				var transaction_id = '';
				
				$.each(inputs, function (index){
					var name = $(this).attr("name");
					var value = $(this).attr("value");
					var type = $(this).attr("type");
					
					if(name == "base_url")
						base_url = value;
					
					if(name == "transaction_id")
						transaction_id = value;
				});
				
				$.ajax({
					type: 'POST',
					headers: { "cache-control": "no-cache" },
					url: base_url + 'index.php?rand=' + new Date().getTime(),
					async: true,
					cache: false,
					dataType: 'json',
					data: formData,
					contentType: false,
					processData: false,
					success: function(data)
					{
						if(!data.errors)
						{
							$("#transation_block_" + transaction_id).fadeOut("slow", function(){
								var newcontent = $(data.next_step).hide();
								$(this).replaceWith(newcontent);
								$('.fotorama').fotorama();
								$("#transation_block_" + transaction_id).fadeIn("slow");
							});
						}
					}});

			})
		});

$(document).ready(function(){
	$(document).off('click', '.transactionactionpanel_submit').on('click', '.transactionactionpanel_submit', function(e){
		e.preventDefault();	
		var parent_form = $(e.target).closest("form");
		var inputs = $(parent_form).find("input");
		
		var formData = new FormData($(e.target).closest("form")[0]);
		
		var transaction_id = '';
		var base_url = '';
				
		$.each(inputs, function (index){
			var name = $(this).attr("name");
			var value = $(this).attr("value");
			var type = $(this).attr("type");
			
			if(name == "transaction_id")
				transaction_id = value;
			
			if(name == "base_url")
				base_url = value;
			
			if(name == "current_step")
				return;//demo mode
		});
		
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: base_url + 'index.php?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType: 'json',
			data: formData,
			contentType: false,
			processData: false,
			success: function(data)
			{
				if(!data.errors)
				{
					$("#transation_block_" + transaction_id).fadeOut("slow", function(){
						var newcontent = $(data.next_step).hide();
						$(this).replaceWith(newcontent);
						$('.fotorama').fotorama();
						$("#transation_block_" + transaction_id).fadeIn("slow");
					});
				}else{
					$("#error_"+transaction_id).text(data.errors);
				}
			}});

	});
});

