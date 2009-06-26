<?php

class ModelFiltersHelper
{
	public static $availableFilters = array('Atomik_Model_Field_Timestamp');
	
	public function modelFilters(Atomik_Model_Builder $builder, $columns = null)
	{
		if ($columns === null) {
			$columns = array_keys($builder->getFields());
		}
		
		$filters = array();
		foreach ($columns as $column) {
			$field = $builder->getField($column);
			if (in_array($className = get_class($field), self::$availableFilters)) {
				$filters[$field->name] = substr($className, strrpos($className, '_') + 1);
			}
		}
		
		if (!count($filters)) {
			return '';
		}
		
		Atomik_Backend_Assets::addStyle('css/filter.css', 'db');
		
		$dirs = dirname(__FILE__) . '/../';
		$html = '<div class="sidebar-box model-filters"><div class="sidebar-box-title">Filters</div><ul>';
		
		foreach ($filters as $name => $filter) {
			$html .= sprintf('<li>By %s %s</li>', strtolower($name), Atomik::helper('modelFilter' . $filter, array($name), $dirs));
		}
		
		$html .= '</ul></div>';
		return $html;
	}
}