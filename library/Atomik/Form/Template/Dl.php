<form <?php echo $this->getAttributesAsString() ?>>
	  
	<dl>
		<?php foreach ($this->getFields() as $field): ?>
			<dt><label for="<?php echo $field->getName() ?>"><?php echo $field->getLabel() ?></label></dt>
			<dd><?php echo $field->render() ?></dd>
		<?php endforeach; ?>
		
		<dt class="buttons"></dt>
		<dd class="buttons">
			<input type="submit" />
		</dd>
	</dl>
	
</form>