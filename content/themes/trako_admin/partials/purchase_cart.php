<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<div class="row">
	<div class="col-sm-12">

		<div class="row">
			<div class="col-sm-9">
				<?php echo  form_open(site_url('ajax/add_to_cart'),['id'=>"add_item_form", "class"=>"content-form"]) ?>
					<div class="form-group">
					<label class="control-label" for="item"> <?=lang('sales_product_label') ?>
							<a href="<?=site_url('products/new')?>">
								<small>(<?=lang('sales_add_product') ?>)</small></a></label>
						<div class="row">
							<div class="col-sm-9">
							<input name="item" id="item" placeholder="<?=lang('sales_add_product_desc') ?>" class="form-control" />
								<input type="hidden" id="cart_name" name="cart" value="purchase"/>
							</div>
							<div class="col-sm-3 m-t-5">
								<span class="btn btn-block btn-primary btn-sm" id="add_to_cart">
									<span class="fa fa-cart-plus"></span>
									<span class="hidden-xs hidden-sm">Add to Cart</span></span></div>
						</div>
					</div>
				<?php echo form_close() ?>
				<div  class="panel">
					<div  class="panel-body-table">
						<div class="table-responsive">
							<table class="table table-condensed table-hover">
								<thead>
									<tr>
										<th>sn</th>
										<th>Description</th>
										<th>Quantity</th>
										<th>price (<?=config_item('currency') ?>)</th>
										<th align="right">Amount (<?=config_item('currency') ?>)</th>
										<th width="50px">
											<span class="fa fa-spinner text-danger fa-spin loader hidden"></span></th>
									</tr>
								</thead>
								<tbody id="cart_contents">
									<?php echo $cart_content['cart_items']; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
			<div class="col-sm-3">
				<?php echo  form_open(site_url('ajax/add_cart_people'),['id'=>"add_people_form", "class"=>"people-form"]) ?>
				<div class="form-group">
					<label class="control-label" for="sender"><?=lang('supplier_name_label') ?>
						<a href="<?=site_url('customers/new') ?>">
							<small>(<?=lang('sales_add_customer') ?>)</small></a></label>
					<input class="form-control" value="<?=set_value('name', $cart_content['people']['name'],true) ?>" name="name" id="people" data-people="2"  />
					<input type="hidden" id="cart_name" name="cart" value="purchase"/>
				</div>
				<?=form_close() ?>
				<div class="panel">
					<div class="panel-body-table">
						<table class="table table-condensed">
							<tbody style="font-weight: 500" id="payment_content">
								<tr>
									<td>Sub Total:</td>
									<td align="right">
										<div class="text-black" data-subtotal="<?=$cart_content['subtotal'] ?>" id="subtotal"><?=my_number_format($cart_content['subtotal'],1) ?></div></td>
								</tr>
								<?php //print_r($cart_content);exit; ?>
								<tr class="paid_amount hidden">
									<td>Payment:</td>
									<td align="right">
										<input name="amountpaid" id="amountpaid" value="<?php echo set_value('amountpaid', $cart_content['payment']) ?>" type="text" style="width:100px"/></td>
								</tr>
								<tr style="font-size: 1.9rem">
									<td>Total</td>
									<td align="right">
										<div class="text-black" data-total="<?=$cart_content['total'] ?>" id="total"><?=$cart_content['total'] ?></div></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="text-uppercase">
			<a href="<?=site_url('purchases/save') ?>" class="btn btn-info btn-sm save-cart savenew">
				<span class="fa fa-check"></span>
				<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></a>

			<a href="<?=site_url('purchases/save') ?>" type="submit" class="btn btn-info btn-sm save-cart saveclose">
				<span class="fa fa-save"></span>
				<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></a>

			<a href="<?=site_url('preview/invoice') ?>" class="btn btn-warning btn-sm">
				<span class="fa fa-eye"></span>
				<span class="hidden-xs hidden-sm"><?=lang('btn_preview') ?></span></a>

			<a href="<?=site_url(previous_url('purchases',true)) ?>" class="cancle btn btn-default btn-sm">
				<span class="fa fa-close"></span>
				<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
		</div>
	</div>
</div>