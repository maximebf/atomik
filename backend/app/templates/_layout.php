<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Atomik Backend</title>
		<link rel="stylesheet" type="text/css" href="<?php echo config_get('base_url') ?>styles/main.css" />
		<script type="text/javascript" src="<?php echo config_get('base_url') ?>scripts/jquery-1.2.6.min.js"></script>
		<script type="text/javascript" src="<?php echo config_get('base_url') ?>scripts/jquery-ui-personalized-1.5.min.js"></script>
		<script type="text/javascript" src="<?php echo config_get('base_url') ?>scripts/main.js"></script>
	</head>
	<body>
		<div id="body">
			<div id="header">
				<div id="header-info">
					Logged in as Admin | Log out | View site
				</div>
				<h1>Atomik Backend</h1>
				
				<ul id="menu">
					<?php foreach (Atomik_Backend::getModules() as $controller => $module): ?>
						<li class="<?php if (Atomik_Backend::getModuleName() == $controller) echo 'selected '; echo $module[1]; ?>">
							<a href="<?php echo get_url($controller) ?>">
								<?php echo __(ucfirst($module[0])); ?>
							</a>
						</li>
						
					<?php endforeach; ?>
				</ul>
				<div class="clear"></div>
			</div>
			
			<?php if (count_flash_messages()): ?>
				<div id="messages">
					<?php foreach (get_flash_messages() as $label => $messages): ?>
						<?php foreach ($messages as $message): ?>
							<div class="message <?php echo $label ?>">
								<?php echo $message ?>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
				<script type="text/javascript">setTimeout('hideFlashMessages()', 5000);</script>
			<?php endif; ?>
			
			<?php echo $content_for_layout ?>
			
			<div id="footer">
				Thank you for using Atomik Framework. 
				<a href="http://www.atomikframework.com">www.atomikframework.com</a>
			</div>
		</div>
	</body>
</html>
