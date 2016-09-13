{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: "PayPal Standard" payment form template
**
** This template is displayed on the payment page and called by the Payment hook
**
** Step 1: The customer is validating this form by clicking on the PayPal payment button
** Step 2: All parameters are sent to PayPal including the billing address to pre-fill a maximum of values/fields for the customer
** Step 3: The transaction success or failure is sent to you by PayPal at the following URL: http://www.mystore.com/modules/paypalusa/controllers/front/validation.php?pps=1
** This step is also called IPN ("Instant Payment Notification")
** Step 4: The customer is redirected to his/her "Order history" page ("My account" section)
*}
<div>
<label><font size="4"><b>第一步： 选择支付方式<b></font></label><br>
<label><font size="2">如果选用支付宝付款目前只支持直接付款，请手工填入收款人<b> subo.sui@gmail.com <b>和支付金额 {$total}</font></label><br>
<br>
<br> 
<div> 
	<span style="float: right;">
	<p class="payment_module_transaction">
		<a href="https://www.alipay.com/"><img style="width: 200px" src="{$base_url}/modules/transactionactionpanel/views/img/alipay.jpg"></a> 		
	</p>
	</span> 
	<span style="loat: left;">
		<form action="{$paypal_usa_action|escape:'htmlall':'UTF-8'}" style="width: 250px" target="_blank"
			method="post" >
			<p class="payment_module_transaction">
				<input type="hidden" name="cmd" value="_xclick" /> <input
					type="hidden" name="business" value="subo.sui@gmail.com" /> <input
					type="hidden" name="currency_code" value="USD" /> <input
					type="hidden" name="amount" value="{$total}" /> <input
					type="hidden" name="custom" value="{$custom}" /> <input
					type="hidden" name="notify_url" value="{$notify_url}" /> <input
					type="hidden" name="return" value="{$return_url}" /> <input
					type="hidden" name="cancel_return" value="{$cancel_url}" /> <input
					type="hidden" name="no_shipping" value="1" /> <input type="hidden"
					name="bn" value="DistantService_BuyNow" /> <input style="width: 200px;border: none;padding: 0px"
					id="paypal-standard-btn" type="image" name="submit"
					src="https://www.paypalobjects.com/en_US/i/bnr/horizontal_solution_PPeCheck.gif"
					alt="" style="vertical-align: middle; margin-right: 10px;" />
			</p>
		</form>
	</span>
</div>

<label><font size="4"><b>第二步： 提交支付信息 （仅限支付宝）<b></font></label><br>
<label><font size="2">如果选用支付宝付款请填入交易流水号，并点击提交支付信息，Paypal支付直接等待支付确认</font></label><br>
{if $alipay_trarnsaction_id != null}
<label>您输入的流水号为:</label><br>
<label>{$alipay_trarnsaction_id}</label><br>
<label>我们将尽快给您确认</label><br>
{/if}
<br>
</div>
