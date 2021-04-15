<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<span><?=lang('report_title') ?></span>
</h3>
<hr />

<div class="row">
	<div class="col-lg-4 col-md-6    col-sm-6 col-xs-12">
		<div class="report-box">
			<a href="<?=site_url('report/sales')?>">SALES</a>
		</div>
	</div>
	
	<div class="col-lg-4 col-md-6    col-sm-6 col-xs-12">
		<div class="report-box">
			<a href="<?=site_url('report/inventory') ?>">INVENTORY</a>
		</div>
	</div>
	
	<div class="col-lg-4 col-md-6    col-sm-6 col-xs-12">
		<div class="report-box">
			<a href="<?=site_url('report/customers') ?>">CUSTOMERS</a>
		</div>
	</div>
	
	<div class="col-lg-4 col-md-6    col-sm-6 col-xs-12">
		<div class="report-box">
			<a href="<?=site_url('report/suppliers') ?>">SUPPLIERS</a>
		</div>
	</div>
	
	<div class="col-lg-4 col-md-6    col-sm-6 col-xs-12">
		<div class="report-box">
			<a href="<?=site_url('report/physical_stock') ?>">PHYSICAL STOCK</a>
		</div>
	</div>
	
	<div class="col-lg-4 col-md-6    col-sm-6 col-xs-12">
		<div class="report-box">
			<a href="">FINANCIAL REPORT</a>
		</div>
	</div>
	
</div>