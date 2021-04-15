<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<div class="row">
	<div class="col-sm-12">
		<h3 class="text-uppercase text-center">Elitraco Nigeria Enterprises</h3></div></div>
<div class="row">
	<div class="col-sm-12">
		<h5 class="text-capitalize text-center m-0">66 A-Line lock-up shop Ariaria international mkt aba, abia state.</h5></div></div>
<div class="row">
	<div class="col-sm-12">
		<h5 class="text-capitalize text-center">090898326723, 326783266782, elitraco@email.com</h5></div></div>
<div class="row">
	<div class="col-sm-12">
		<h4 class="text-uppercase text-center"><?=$transaction->transaction ?></h4></div></div>
<div class="row" style="font-size: 1.5rem">
	<div class="col-md-8 text-capitalize">
		<?php if($transaction->name !== NULL): ?>
		<P>
			<strong><?=$transaction->name_title ?>:</strong> <?=$transaction->name ?><br /></P>
		<P>
		 <?php endif?>
			<strong>Amount:</strong> <?=$transaction->amount ?><br /></P>
		<?php
		if (!is_null($transaction->from_account))
			: ?><P>
			<strong>From Account:</strong> <?=$transaction->from_account ?><br /></P><?php endif ?>
		<?php
		if (!is_null($transaction->to_account))
			: ?><P>
			<strong>To Account:</strong> <?=$transaction->to_account ?><br /></P> <?php endif?>
		<P>
			<strong>Description:</strong> <?=$transaction->memo ?> <br /></P>
		<P>
			<strong>Processed by:</strong> <?=$transaction->staff ?></P>
	</div>
	<div class="col-md-4">
		<P class="text-right">
			<strong>Payment Date: </strong>
			<span><?=$transaction->date ?></span>
		</P>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<hr />
	</div>
</div>
<div class="text-center p-t-20">
	<a class="btn btn-info btn-sm" href="<?=site_url('payments/edit/'.$transaction->id) ?>">
		<span class="fa fa-edit" ></span> Edit</a>
	<a class="btn btn-danger btn-sm" href="<?=site_url('payments/delete/'.$transaction->id) ?>">
		<span class="fa fa-trash" ></span> Delete</a>
	<a class="btn btn-info btn-sm"href="">
		<span class="fa fa-print" ></span> Print</a>
	<a class="btn btn-default btn-sm" href="javascrit:void(0)" onclick="window.history.back()">
		<span class="fa fa-angle-left" ></span> Go Back</a>
</div>

