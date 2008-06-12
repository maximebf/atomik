<div id="main-wrapper">
	<div id="main">
		<h1><?php echo __('Pages') ?></h1>
		<div id="content">
			<?php if(count($templates) > 0): ?>
				<dl id="tree" class="file-list tree">
					<?php
						foreach ($templates as $template) {
							echo Atomik::render('pages/_page', array('template' => $template));
						}
					?>
				</dl>
			<?php else: ?>
				<p>
					<strong>It seems that none of your pages are editable.</strong>
				</p>
				<p>
					Have a look to the documentation to discover how to make your pages
					editable from the backend.
				</p>
			<?php endif; ?>
		</div>
	</div>
</div>
<div id="sidebar-wrapper">
	<div id="sidebar">
		
	</div>
</div>
