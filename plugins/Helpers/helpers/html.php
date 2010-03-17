<?php

class HtmlHelper extends Atomik_Helper
{
    /**
     * Builds an html string from an array
     * 
     * Array's value can be a string or if the key is a string, which in this
     * case represent a tag name, an array.
     * Attributes start with @. Attributes can be defined as one array using
     * the "@" key (in this case, the attributes array keys do not need to start
     * with @).
     *
     * @param string|array $html
     * @param string $tag OPTIONAL Wrap the html into an element with this tag name
     * @param bool $dimensionize OPTIONAL Whether to use Atomik::_dimensionizeArray()
     * @return string
     */
    public function html($html, $tag = null, $dimensionize = false)
    {
    	if ($tag !== null) {
    		/* building a proper $html array using the tag */
    		return array_to_html(array($tag => $html));
    	}
    	
    	if (is_string($html)) {
    		/* no need to parse */
    		return $html;
    	}
    	
    	if ($dimensionize) {
    		/* dimensionizing the array */
    		$html = Atomik::_dimensionizeArray($html);
    	}
    	
    	$output = '';
    	
    	foreach ($html as $tag => $value) {
    		/* do not parse attributes */
    		if (substr($tag, 0, 1) == '@') {
    			continue;
    		}
    		/* the key does not represent a tag */
    		if (!is_string($tag)) {
    			if (!is_array($value)) {
    				$output .= $value;
    				continue;
    			}
    		}
    		/* the key is a tag */
    		$attributes = array();
    		if (is_array($value)) {
    			/* has more than one children */
    			if (isset($value['@'])) {
    				/* using the @ key for setting attributes */
    			    $attributes = $value['@'];
    			} else {
    				/* no @ key found, checking through all sub keys for the ones
    				 * starting with @ */
    				foreach ($value as $attrName => $attrValue) {
    					if (substr($attrName, 0, 1) == '@') {
    						$attributes[substr($attrName, 1)] = $attrValue;
    					}
    				}
    			}
    			/* parsing the value */
    			$keys = array_keys($value);
    			if (!is_string($keys[0]) && is_array($value[0])) {
    				foreach ($value as $item) {
    					$output .= array_to_html($item, $tag);
    				}
    				break;
    			} else {
    				$value = array_to_html($value);
    			}
    		}
    		
    		/* builds the html string */
    		$output .= sprintf("<%s %s>%s</%s>\n", 
    		    $tag,
    		    $this->helpers->htmlAttributes($attributes), 
    		    $value, 
    		    $tag
		    );
    	}
    	
    	return $output;
    }
}
