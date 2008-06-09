<?php

	/**
	 * Parses a template and retreives cms fields
	 *
	 * @param string $filename
	 * @return array
	 */
	function cms_parse_template($filename)
	{
		$template = file_get_contents($filename);
		
		/* checks if the function cms_page() is in the template */
		if (!preg_match('/cms_page\((?:"|\')(.+)(?:"|\')/iU', $template, $match)) {
			return false;
		}
		$page = $match[1];
		
		/* finds all fields */
		$regexp = '/cms_content_([a-z]+)\((?:"|\')(.+)(?:"|\'),(?:\s|)(?:"|\')(.+)(?:"|\')/U';
		preg_match_all($regexp, $template, $matches);
		
		/* builds the fields array */
		$fields = array();
		for ($i = 0, $count = count($matches[0]); $i < $count; $i++) {
			$fields[$matches[2][$i]] = array(
				'type' => $matches[1][$i],
				'label' => $matches[3][$i]
			);
		}
		
		return array($page, $fields);
	}
