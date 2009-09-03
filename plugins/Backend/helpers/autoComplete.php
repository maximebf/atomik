<?php

class AutoCompleteHelper
{
	public function autoComplete($id, $data)
	{
		if (isset($_GET['autoComplete']) && $_GET['autoComplete'] == $id) {
			ob_clean();
			$this->handle($data, $_GET['q']);
			Atomik::end(true);
		}
		
		Atomik_Assets::addNamedAsset('autoComplete');
		
		return sprintf('<script type="text/javascript">jQuery(function($) { $("#%s").autocomplete("%s"); });</script>',
			$id, Atomik::url(null, array('autoComplete' => $id)));
	}
	
	public function handle($data, $query)
	{
		$filteredData = $this->getFilteredData($data, strtolower($query));
		
		$output = array();
		foreach ($filteredData as $k => $v) {
			$output[] = $v . '|' . $k;
		}
		
		echo implode("\n", $output);
	}
	
	public function getFilteredData($data, $query)
	{
		$filteredData = array();
		foreach ($data as $k => $v) {
			if (strtolower(substr($v, 0, strlen($query))) == $query) {
				$filteredData[$k] = $v;
				if (count($filteredData) == 10) {
					break;
				}
			}
		}
		return $filteredData;
	}
}