<form action="<?php echo $this->getAction() ?>" 
	  enctype="<?php echo $this->getEnctype() ?>" 
	  method="<?php echo $this->getMethod() ?>"
	  <?php echo $this->getAttributesAsString() ?>>
	  
	<ul>
		<?php foreach ($fields as $field): ?>
			<li>
				<label for="<?php echo $field->getName() ?>"><?php echo $field->getLabel() ?></label>
				<?php echo $field->render($this) ?>
			</li>
		<?php endforeach; ?>
		<li class="buttons">
			<input type="submit" />
		</li>
	</ul>
	
</form>