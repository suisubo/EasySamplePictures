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
<form action="{$paypal_usa_action|escape:'htmlall':'UTF-8'}" method="post">
	<p class="payment_module">
		<input type="hidden" name="cmd" value="_xclick" />
		<input type="hidden" name="business" value="subo.sui@gmail.com" />
		<input type="hidden" name="currency_code" value="USD" />
		<input type="hidden" name="amount" value="{$total}" />
		<input type="hidden" name="custom" value="{$custom}" />
		<input type="hidden" name="notify_url" value="{$notify_url}" />
		<input type="hidden" name="return" value="{$return_url}" />
		<input type="hidden" name="cancel_return" value="{$cancel_url}" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="bn" value="DistantService_BuyNow" />
		<input id="paypal-standard-btn" type="image" name="submit" src="https://www.paypalobjects.com/en_US/i/bnr/horizontal_solution_PPeCheck.gif" alt="" style="vertical-align: middle; margin-right: 10px;" /> {l s='Pay with PayPal'}
	</p>
</form>
