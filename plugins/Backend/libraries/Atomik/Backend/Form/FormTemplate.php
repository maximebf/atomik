<?php $this->setAttribute('class', trim($this->getAttribute('class', '') . ' form form-content')) ?>
<form <?php echo $this->getAttributesAsString() ?>>
	  
	<dl>
		<?php echo $this->renderFields() ?>
		
		<dt class="buttons"></dt>
		<dd class="buttons">
			<input type="submit" value="<?php echo __('Save') ?>" />
			<a href="<?php echo $this->getOption('cancel-url', 'javascript:history.back(-1)') ?>" class="form-link-button"><?php echo __('Cancel') ?></a>
		</dd>
	</dl>
	
</form>