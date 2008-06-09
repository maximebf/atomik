<h1><?php echo $pageTitle ?></h1>
<form>
	<?php 
	
		foreach ($fields as $id => $field) {
			echo '<p><label>' . $field['label'] . '</label>';
			switch ($field['type']) {
				case 'input':
					echo '<input type="text" name="' . $id . '" />';
					break;
					
				case 'textarea':
					echo '<textarea name="' . $id . '"></textarea>';
					break;
			}
			echo '</p>';
		}
		
	?>
</form>
