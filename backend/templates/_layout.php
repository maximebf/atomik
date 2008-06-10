<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Atomik Backend</title>
		<link rel="stylesheet" type="text/css" href="styles/main.css" />
		<script type="text/javascript" src="scripts/jquery-1.2.6.min.js"></script>
		<script type="text/javascript" src="scripts/main.js"></script>
	</head>
	<body>
		<div id="body">
			<div id="header">
				<div id="header-info">
					Logged in as Admin | Log out | View site
				</div>
				<h1>Atomik Backend</h1>
				
				<ul id="menu">
					<li><a href="<?php echo get_url('pages/index') ?>" class="current">Pages</a></li>
					<li><a href="<?php echo get_url('snippets/index') ?>">Snippets</a></li>
					<li><a href="<?php echo get_url('layouts/index') ?>">Layouts</a></li>
					<li class="right"><a href="<?php echo get_url('doc/index') ?>">Documentation</a></li>
					<li class="right"><a href="<?php echo get_url('admin/index') ?>">Administration</a></li>
				</ul>
				<div class="clear"></div>
			</div>
			<div id="messages">
				<?php foreach (get_flash_messages() as $label => $messages): ?>
					<?php foreach ($messages as $message): ?>
						<div class="message <?php echo $label ?>">
							<?php echo $message ?>
						</div>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</div>
			<div id="main">
				<?php echo $content_for_layout ?>
			</div>
			<div id="footer">
				Thank you for using Atomik
			</div>
		</div>
	</body>
</html>
