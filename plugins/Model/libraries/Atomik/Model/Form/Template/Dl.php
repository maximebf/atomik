<form action="<?php echo $this->getAction() ?>" 
	  enctype="<?php echo $this->getEnctype() ?>" 
	  method="<?php echo $this->getMethod() ?>"
	  <?php echo $this->getAttributesAsString() ?>>
	  
	<dl>
		<?php foreach ($fields as $field): ?>
			<dt><label for="<?php echo $field->getName() ?>"><?php echo $field->getLabel() ?></label></dt>
			<dd><?php echo $field->render($this) ?></dd>
		<?php endforeach; ?>
		<dt class="buttons"></dt>
		<dd class="buttons">
			<input type="submit" />
		</dd>
	</dl>
	
</form>