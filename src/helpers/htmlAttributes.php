<?php

class HtmlAttributesHelper extends Atomik_Helper
{
    public function htmlAttributes($array, $filter = null, $exclude = true)
    {
    	$attrs = array();
    	foreach ($array as $key => $value) {
    		if (!empty($filter) && ((!$exclude && !in_array($key, (array) $filter)) || 
    			($exclude && in_array($key, (array) $filter)))) {
    			continue;
    		}
    		$attrs[] = sprintf('%s="%s"', $key, $value);
    	}
    	return implode(' ', $attrs);
    }
}