<!DOCTYPE html>
<html  lang="<?= $this->session->language_abbr ?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/hg_admin/images/haselgroup_logo_small.png'); ?>">

		<title><?php echo Events::trigger('header_title',$template['title'], 'string'); ?></title>
		<?php echo $template['css_files']?>
		<script>
			var global_vars = {base_url:"<?php echo admin_url()?>",'empty_sales_message':'unable to add item to cart','currency':"<?=config_item('currency')?>","__back":"<?php echo$this->agent->referrer()?>"};
		</script>



		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

	</head>

	<body class="<?php echo Events::trigger('body_class', '', 'string'); ?>">
		<!--[if lte IE 9]>
        	<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    	<![endif]-->
		
		
	
		<div class="<?php echo Events::trigger('parent_div_class', '', 'string'); ?>">
    
			<?php echo $template['partials']['header']?>
    	
			<?php echo $template['partials']['sidebar']?>
			<!-- ============================================================== -->
			<!-- Page Content -->	
			<!-- ============================================================== -->
			<div id="page-wrapper">
				<div class="container-fluid">
					<?php echo $template['partials']['titlebar']; ?>
                
					<?php echo $template['partials']['flashdata']; ?>
				
					<div class="row">
						<div class="col-md-12">
							
							<div class="<?=(!isset($dashboard))?'white-box':''?>">
								<?php echo $template['body']; ?>
							</div>
						</div>
					</div>
				</div>
        	
				<!-- /.container-fluid -->
				<?php echo $template['partials']['footer']; ?>
            	<div class="right-sidebar">
                    <div class="slimscrollright">
                        <div class="rpanel-title"> Shortcut Panel <span><i class="fa fa-close right-side-toggle"></i></span> </div>
                        <div class="r-panel-body">
                            <ul class="chatonline">
                                <li><b>Jump to any action</b></li>
                                <li><a href="#"><span> <i class="fa fa-sticky-note fa-fw"></i>Add new post</span></a></li>
                                <li><a href="#"><span> <i class="fa fa-user-plus fa-fw"></i>Add new user</span></a></li>
                                <li><a href="#"><span><i class="fa fa-envelope fa-fw"></i>Compose new message</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
			</div>
			<!-- ============================================================== -->
			<!-- End Page Content -->
			<!-- ============================================================== -->
			<div id="ajax01-1">

			</div>
		</div>
		<script type="text/javascript" src="<?php echo js_url('jquery.min', 'common'); ?>"></script>

	    <script>window.jQuery || document.write('<script src="<?php echo js_url('jquery.min', 'common'); ?>"><\/script>')</script>
		<?php echo $template['js_files']; ?>

		<?php if (config_item('ga_enabled') && (! empty(config_item('ga_siteid')) && config_item('ga_siteid') <> 'UA-XXXXX-Y')): ?>
		    <!-- Google Analytics-->
		    <script>
		        window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
		        ga('create','<?php echo config_item('ga_siteid'); ?>','auto');ga('send','pageview')
		    </script>
		    <script src="https://www.google-analytics.com/analytics.js" async defer></script>
		<?php endif; ?>
	</body>

	<!-- Mirrored from www.ampleadmin.wrappixel.com/ampleadmin-html/ampleadmin-sidebar/login2.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 28 Jul 2018 11:44:11 GMT -->
</html>