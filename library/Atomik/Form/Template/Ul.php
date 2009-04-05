<form <?php echo $this->getAttributesAsString() ?>>
	  
	<ul>
		<?php foreach ($fields as $field): ?>
			<li>
				<label for="<?php echo $field->getName() ?>"><?php echo $field->getLabel() ?></label>
				<?php echo $field->render() ?>
			</li>
		<?php endforeach; ?>
		
		<li class="buttons">
			<input type="submit" />
		</li>
	</ul>
	
</form>