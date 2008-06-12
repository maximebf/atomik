<div id="main-wrapper">
	<div id="main">
		<h1><?php echo __($template->getName()); ?></h1>
		<div id="content">
			<form action="<?php echo Atomik::url('pages/save'); ?>" method="post" class="form form-content">
				<input type="hidden" name="file" value="<?php echo $file; ?>" />
				<dl>
					<?php foreach ($template->getFields() as $field): ?>
						<dt>
							<?php echo __($field['label']); ?>
						</dt>
						<dd>
							<?php
								/* checks if value does not exists which means that
								 * there is still no row in the database for this field */
								if ($field['new']) {
									echo '<input type="hidden" name="newFields[' . $field['id'] . ']" value="1" />';
								}
								
								/* prints the input associated to the field type */
								switch ($field['type']) {
									case 'input':
										echo '<input name="fields[' . $field['id'] . ']" type="text" value="' 
											. $field['value'] . '" class="form-input" />';
										break;
									
									case 'textarea':
										echo '<textarea name="fields[' . $field['id'] . ']" class="form-textarea">' 
											. $field['value'] . '</textarea>';
										break;
								}
							?>
						</dd>
					<?php endforeach; ?>
					<dt></dt>
					<dd class="buttons">
						<input type="submit" value="<?php echo __('Save'); ?>" class="form-button" />
						<?php echo __('or'); ?> 
						<a href="<?php echo Atomik::url('pages/index'); ?>" class="form-link-button" title="<?php echo __('Cancel'); ?>">
							<?php echo __('Cancel'); ?>
						</a>
					</dd>
				</dl>
			</form>
		</div>
	</div>
</div>
<div id="sidebar-wrapper">
	<div id="sidebar">
		
	</div>
</div>
