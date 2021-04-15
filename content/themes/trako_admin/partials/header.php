<nav class="navbar navbar-default navbar-static-top m-b-0">
	<div class="navbar-header">
		<div class="top-left-part">
			<!-- Logo -->
			<a class="logo" href="<?=site_url(ADMIN).config_item('default_group_permission_name')?>">
				<!-- Logo icon image, you can use font-icon also -->
				<b>
					<img src="<?=upload_url("ecosooft_logo.png")?>" alt="s" class="light-logo" />
				</b>
				<!-- Logo text image you can use text also -->
				<span class="hidden-xs">
					<img src="<?=upload_url("ecosooftsmall.png")?>" alt="<?php echo $site_name?>" class="light-logo" />
				</span>
			</a>
		</div>
		<ul class="nav navbar-top-links navbar-left">
			<li>
				<a href="javascript:void(0)" class="open-close waves-effect waves-light">
					<i class="fa fa-align-right">
					</i>
				</a>
			</li>
		</ul>

		<!-- /Logo -->
		<ul class="nav navbar-top-links navbar-right pull-right">
			<li class="dropdown">

                <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" aria-expanded="false">
                	<i class="fa fa-bell text-info"></i>
                    <div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                </a>

                <ul class="dropdown-menu mailbox animated slideInDown">
                    <li>
                        <div class="drop-title">You have 1 new notifications</div>
                    </li>
                    <li>
                        <div class="message-center">
                            <a href="#">
                                <div class="mail-contnet">
                                    <span class="mail-desc">Just see the my admin!</span> 
                                    <span class="time">9:30 AM</span> 
                                </div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a class="text-center" href="javascript:void(0);"> <strong>See all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                    </li>
                </ul>
                <!-- /.dropdown-messages -->
            </li>
			<li class="dropdown">
				<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
					<b class="">
						Hi, <?=$_user_details->first_name?>
						<span class="hidden-xs" style="background-color:#d1f3b5;font-weight: 700;font-size: 1.3em; color: #3c701f; margin-left: 5px;padding: 5px 10px; border-radius: 15%">E</span>
					</b>
				</a>
				<ul class="dropdown-menu dropdown-user animated slideInDown text-capitalize">
                    <li>
                        <div class="dw-user-box">
                            <div class="u-text">
                            	<span class="hidden-xs" style="background-color:#d1f3b5;font-weight: 700;font-size: 1.3em; color: #3c701f; margin-left: 5px;padding: 5px 10px; border-radius: 15%"><?=$_user_full_name[0]?></span>
                            	<?=$_user_full_name?>
                            </div>
                            
                        </div>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li><a href="<?=site_url('my_account')?>"><i class="fa fa-user"></i> My Profile <div class="help-block text-muted">my_account settings and more</div></a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="<?=site_url('messages')?>"><i class="fa fa-line-chart"></i> Sales today <small class="btn btn-xs btn-danger pull-right">2</small></a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="<?=site_url(ADMIN.'my_account/logout')?>" class=""><i class="fa fa-power-off"></i> Logout</a></li>
                </ul>
			</li>
		</ul>
	</div>
	<!-- /.navbar-header -->
	<!-- /.navbar-top-links -->
	<!-- /.navbar-static-side -->
</nav>