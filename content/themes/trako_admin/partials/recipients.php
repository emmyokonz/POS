<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<div class="modal fade" id="recipients" tabindex="-1" role="dialog" aria-labelledby="recipientsLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header box-title">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="agentLabel"><?=lang('sms_title_choose_recipients') ?></h5>
			</div>
			<form action="<?=site_url('sms/add_recipients_ajax') ?>" method="post" id="recipients_form">
				<div class="modal-body">
					<div class="vtabs">
						<ul class="nav tabs-vertical">
							<li class="tab active">
								<a data-toggle="tab" href="#home3" aria-expanded="false">
									<span class="visible-xs">
										<i class="ti-home"></i></span>
									<span class="hidden-xs">All</span> </a>
							</li>
						</ul>
						<div class="tab-content p-t-0">
							<div id="home3" class="tab-pane active">
								<div class="table-responsive">
									<table class="table table-hover table-bordered">
										<thead>
											<tr>
												<th></th>
												<th>Name</th>
												<th>Phone</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>fh</td>
												<td>hgdfgsjdfs g</td>
												<td>09098987877</td>
												<td><button class="btn btn-danger btn-xs"><span class="fa fa-trash"></span></button></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('btn_cancle') ?></button>
					<button type="submit" class="btn btn-primary" id="action"><?=lang('btn_add_recipients') ?> </button>
				</div>
			</form>
		</div>
	</div>
</div>