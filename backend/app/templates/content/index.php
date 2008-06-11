
<div id="main">
	<h1><?php echo __('Content'); ?></h1>
	<div id="content">
		<ul class="file-list">
			<?php foreach ($templates as $template): ?>
				<li class="file-list-row">
					<a href="<?php echo get_url('content/edit&type=' . $template->getTable()); ?>" class="file-list-name">
						<?php echo $template->getName(); ?>
					</a>
					<div class="file-list-actions">
						<a href="<?php echo get_url('content/create&type=' . $template->getTable()); ?>">
							<img src="images/plus.png" alt="New" title="Create a new <?php echo $template->getName(); ?>" />
						</a>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
