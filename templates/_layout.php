<html>
	<head>
		<title>Atomik</title>
	</head>
	<body>
	
		<?php foreach (get_flash_messages() as $message): ?>
			<h2><?php echo $message ?></h2>
		<?php endforeach; ?>
	
		<?php echo $content_for_layout; ?>
	</body>
</html>
