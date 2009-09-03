<?php

class ModelSearchHelper
{
	public function modelSearch(Atomik_Model_Builder $builder)
	{
		$html = '<form action="' . Atomik::url() . '" class="model-search" method="post">Search: '
				. '<input type="text" name="search" value="' . A('search', '', $_POST) . '" /><select name="searchBy">';
		
		foreach ($builder->getFields() as $field) {
			$html .= sprintf("<option value=\"%s\" %s>%s</option>\n", 
				$field->name, 
				isset($_POST['searchBy']) && $_POST['searchBy'] == $field->name ? 'selected="selected"' : '',
				$field->getLabel()
			);
		}
		
		$html .= '</select><input type="submit" value="search" /></form>';
		return $html;
	}
}