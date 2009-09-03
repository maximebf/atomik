<?php 
	Atomik_Backend_Assets::addStyle('css/form.css');
	$this->setAttribute('class', trim($this->getAttribute('class', '') . ' form form-content')) 
?>
<form <?php echo $this->getAttributesAsString() ?>>
	  
	<dl>
		<?php echo $this->renderFields() ?>
		
		<dt class="buttons"></dt>
		<dd class="buttons">
			<?php echo Atomik::helper('formButtons', array(
				$this->getOption('cancel-url', 'javascript:history.back(-1)'),
				$this->getOption('submit-label', 'Save'),
				$this->getOption('cancel-label', 'Cancel')
				)) ?>
		</dd>
	</dl>
	
</form>