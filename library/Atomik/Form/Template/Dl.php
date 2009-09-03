<form <?php echo $this->getAttributesAsString() ?>>
	  
	<dl>
		<?php echo $this->renderFields() ?>
		
		<dt class="buttons"></dt>
		<dd class="buttons">
			<input type="submit" value="<?php echo $this->getOption('button-label', 'Submit') ?>" />
		</dd>
	</dl>
	
</form>