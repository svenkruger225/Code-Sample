<div class="main_container_payment">
	<div class="mian_wrapper_payment">
		<div class="head_payment">
			<div class="main_heading_payment"><span>QA Merchant No 2</span></div>
			<div class="credit_cards_images">
				<ul>
					<li><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/visa.png" /></li>
					<li><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/master_card.png" /></li>
					<li><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/american_express.png" /></li>
					<li><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/orient.png" /></li>
					<li><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/discover.png" /></li>
					<li><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/sears.png" /></li>
				</ul>
			</div>
		</div>
		<div class="head_bar">Credit Card Details</div>
		<div class="credit_card_info">
			<span class="text">Please complete the following <b>bold</b> fields and click Process Transactiona</span>
			<br />
			<br />
			<br />
			<div class="form_item">
				<div class="label"><span class="text">Order ID:</span></div>
				<div class="field"><span class="text">mhp112233445566</span></div>
			</div>
			<div class="form_item">
				<div class="label"><span class="text">Amount ($USD):</span></div>
				<div class="field"><span class="text">$10</span></div>
			</div>
			<br />
			<div class="form_item">
				<div class="label"><span class="text"><b>Name on Card:</b></span></div>
				<div class="field"><input type="text" /></div>
			</div>
			<div class="form_item">
				<div class="label"><span class="text"><b>Credit Card Number:</b></span></div>
				<div class="field"><input type="text" /></div>
			</div>
			<div class="form_item">
				<div class="label"><span class="text"><b>Expiry Date:</b></span></div>
				<div class="field">
					<select>
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
						<option>5</option>
					</select>
					<select>
						<option>2012</option>
						<option>2013</option>
						<option>2014</option>
						<option>2015</option>
						<option>2016</option>
					</select>
				</div>
			</div>
			<br />
			<br />
		</div>
		<div class="head_bar">Item Details</div>
		<div class="item_details">
			<table cellpadding="0" class="item_details">
				<tr>
					<th>ID</th>
					<th>Description</th>
					<th>Qty</th>
					<th>Unit Cost</th>
					<th>Subtotal</th>
				</tr>
				<tr>
					<td>cir001</td>
					<td>Med-Circl</td>
					<td>2</td>
					<td>$2.01</td>
					<td>$4.02</td>
				</tr>
				<tr>
					<td>cir001</td>
					<td>Med-Circl</td>
					<td>2</td>
					<td>$2.01</td>
					<td>$4.02</td>
				</tr>
			</table>
			<br />
		</div>
		<div class="head_bar">Customer Information</div>
		<div class="customer_information">
			<div class="billing_info">
				<div class="info_head">Billing Information</div>
				<div class="info">
					<ul>
						<li>First Name</li>
						<li>Last Name</li>
						<li>Companu Name</li>
						<li>Address</li>
						<li>City</li>
						<li>Prov/State</li>
						<li>Postal/Zip Code</li>
						<li>Country</li>
						<li>Phone</li>
						<li>Fax</li>
					</ul>
				</div>
				<div class="payment_button"><input type="submit" value="Process Payment" /></div>
			</div>
			<div class="vertical_spacer"></div>
			<div class="shipping_info">
				<div class="info_head">Shipping Information</div>
				<div class="info">
					<ul>
						<li>First Name</li>
						<li>Last Name</li>
						<li>Companu Name</li>
						<li>Address</li>
						<li>City</li>
						<li>Prov/State</li>
						<li>Postal/Zip Code</li>
						<li>Country</li>
						<li>Phone</li>
						<li>Fax</li>
					</ul>
				</div>
				<div align="right" class="payment_button"><input type="reset" value="Cancel Transaction" /></div>
			</div>
		</div>
		
	</div>
</div>