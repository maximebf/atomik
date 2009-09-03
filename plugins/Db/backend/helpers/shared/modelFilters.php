<?php

class ModelFiltersHelper
{
	public function modelFilters(Atomik_Model_Builder $builder)
	{
		$filters = Atomik_Model_Query::getAvailableFilters($builder);
		if (!count($filters)) {
			return '';
		}
		
		Atomik_Backend_Assets::addStyle('css/filter.css', 'db');
		Atomik_Backend_Assets::addScript('js/filter.js', 'db');
		
		$html = '<div class="sidebar-box model-filters"><div class="sidebar-box-title">Filters</div><ul>';
		
		foreach ($filters as $name => $filter) {
			$html .= sprintf('<li><span class="by">By %s</span>%s</li>', $builder->getField($name)->getLabel(), $this->_getList($name, $filter->getPossibleValues()));
		}
		
		$html .= '</ul></div>';
		return $html;
	}
	
	protected function _getList($field, $possibleValues)
	{
		$selectedValue = Atomik::get('request/filters/' . $field, '');
		
		$html = '<ul ' . ($selectedValue == '' ? 'style="display:none"' : '') . '>';
		foreach ($possibleValues as $label => $value) {
			$html .= '<li><a ' . ($value == $selectedValue ? 'class="current"' : '') 
				   . ' href="' . $this->_getUrl($field, $value) . '">' . $label . '</a></li>';
		}
		$html .= '</ul>';
		return $html;
	}
	
	protected function _getUrl($field, $value = '')
	{
		return Atomik::url(null, array('filters' => array($field => $value)));
	}
}