<div id="main-wrapper">
	<div id="main">
		<h1><?php echo __('Pages') ?></h1>
		<div id="content">
			<?php if(count($pages) > 0): ?>
				<dl id="tree" class="file-list tree">
					<?php
						foreach ($pages as $page) {
							echo Atomik::render('_page', array('page' => $page));
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
