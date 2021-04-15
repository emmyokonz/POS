
<?php defined('BASEPATH') OR exit('No direct access to script allowed.');?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" type="image/png" sizes="16x16" href="../plugins/images/favicon.png">
		<title>
			<?php echo Events::trigger('header_title',$template['title'], 'string');?>
		</title>
		<?php echo $template['css_files']?>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="fix-header">


		<section id="wrapper" class="new-login-register">
			
			<div class="new-auth-box">
				<div class="white-box">
					<?=$template['body']?>
				</div>
			</div>


		</section>

	</body>
</html>