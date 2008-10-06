<form action="<?php echo $this->getAction() ?>" 
	  enctype="<?php echo $this->getEnctype() ?>" 
	  method="<?php echo $this->getMethod() ?>"
	  <?php echo $this->getAttributesAsString() ?>>
	  
	<?php foreach ($fields as $field): ?>
		<p>
			<label for="<?php echo $field->getName() ?>"><?php echo $field->getLabel() ?></label>
			<?php echo $field->render($this) ?>
		</p>
	<?php endforeach; ?>
	<p class="buttons">
		<input type="submit" />
	</p>
	
</form>