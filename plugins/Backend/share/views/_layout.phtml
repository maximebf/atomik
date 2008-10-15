<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?php 
				$tab = Atomik_Backend::getCurrentTab();
				echo $tab['text'] . ' | '. Atomik::get('backend/title'); 
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="<?php echo Atomik::get('base_url') ?>app/plugins/Backend/assets/styles/main.css" />
		<script type="text/javascript" src="<?php echo Atomik::get('base_url') ?>app/plugins/Backend/assets/scripts/jquery-1.2.6.min.js"></script>
		<script type="text/javascript" src="<?php echo Atomik::get('base_url') ?>app/plugins/Backend/assets/scripts/jquery-ui-personalized-1.5.min.js"></script>
		<script type="text/javascript" src="<?php echo Atomik::get('base_url') ?>app/plugins/Backend/assets/scripts/main.js"></script>
		<?php echo Atomik::fireEvent('Backend::Layout::Head', array(), true); ?>
	</head>
	<body>
		<div id="body">
			<div id="header">
				<div id="header-info">
					Logged in as Admin | Log out | View site
				</div>
				<h1><?php echo Atomik::get('backend/title'); ?></h1>
				
				<ul id="menu">
					<?php echo Atomik::fireEvent('Backend::Layout::Tabs::Before', array(), true); ?>
					<?php foreach (Atomik_Backend::getTabs() as $tab): ?>
						<li class="<?php if (Atomik_Backend::isCurrentTab($tab)) echo 'selected '; echo $tab['position']; ?>">
							<a href="<?php echo Atomik::url($tab['url']) ?>">
								<?php echo __(ucfirst($tab['text'])); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<?php echo Atomik::fireEvent('Backend::Layout::Tabs::After', array(), true); ?>
				</ul>
				<div class="clear"></div>
			</div>
			
			<div id="messages">
				<?php echo Atomik::fireEvent('Backend::Layout::Messages::Before', array(), true); ?>
    			<?php if (Atomik_Session::countMessages()): ?>
					<?php foreach (Atomik_Session::getMessages() as $label => $messages): ?>
						<?php foreach ($messages as $message): ?>
							<div class="message <?php echo $label ?>">
								<?php echo $message ?>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
    				<script type="text/javascript">setTimeout('Atomik.hideMessages()', 5000);</script>
    			<?php endif; ?>
				<?php echo Atomik::fireEvent('Backend::Layout::Messages::After', array(), true); ?>
			</div>
			
			<?php echo Atomik::fireEvent('Backend::Layout::Content::Before', array(), true); ?>
			<?php echo $content_for_layout ?>
			<?php echo Atomik::fireEvent('Backend::Layout::Content::After', array(), true); ?>
			
			<div id="footer">
				Thank you for using Atomik Framework. 
				<a href="http://www.atomikframework.com">www.atomikframework.com</a>
			</div>
		</div>
	</body>
</html>
