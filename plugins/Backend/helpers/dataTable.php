<?php

class DataTableHelper
{
	public function dataTable($id, $data, $options = array())
	{
		$clickableColumns = Atomik::get('clickable_cols', null, $options);
		$idColumn = Atomik::get('id_column', 'id', $options);
		
		$columns = array();
		if (!isset($options['columns'])) {
			// find columns through all rows
			foreach ($data as $row) {
				foreach ($row as $key => $value) {
					if (!isset($columns[$key])) {
						$columns[$key] = $key;
					}
				}
			}
			
		} else {
			// clean the columns array
			foreach ($options['columns'] as $key => $value) {
				if (is_int($key)) {
					$key = $value;
				}
				$columns[$key] = $value;
			}
		}
		
		// builds <thead>
		$thead = "<thead>\n\t<tr>\n";
		foreach ($columns as $label) {
			$thead .= "\t\t<th>" . $label . "</th>\n";
		}
		$thead .= "\t</tr>\n</thead>";
		
		// builds <tbody>
		$tbody = '<tbody>';
		foreach ($data as $row) {
			$rel = isset($row[$idColumn]) ? $row[$idColumn] : '';
			$tbody .= sprintf("\t<tr rel=\"%s\">\n", $rel);
			foreach ($columns as $key => $value) {
				$classes = '';
				if (!isset($row[$key])) {
					$row[$key] = '';
				}
				if ($clickableColumns === null || in_array($key, $clickableColumns)) {
					$classes = 'clickable';
				}
				$tbody .= sprintf("\t\t<td class=\"%s\">%s</td>\n", $classes, $row[$key]);
			}
			$tbody .= "\t</tr>\n";
		}
		$tbody .= '</tbody>';
		
		if (isset($_GET['dataTableRemote']) && $_GET['dataTableRemote'] == $id) {
			// remote call to only get the data
			ob_clean();
			echo '<table><tbody>' . $tbody . '</tbody></table>';
			Atomik::end(true);
		}
		
		// adds needed assets
		Atomik_Backend_Layout::addScript('js/DataTable.js');
		Atomik_Backend_Layout::addStyle('css/datatable.css');
		
		// options
		$options['remote'] = Atomik::pluginUrl(null, array('dataTableRemote' => $id));
		$options['url'] = Atomik::get('url', Atomik::pluginUrl('edit'), $options);
		$jsonOptions = json_encode($options);
		
		// the full html
		$html = sprintf("<table id=\"%s\">\n%s\n%s\n</table>", $id, $thead, $tbody);
		$html .= sprintf('<script type="text/javascript">jQuery(document).ready(function() { jQuery("#%s").dataTable(%s); })</script>', $id, $jsonOptions);
		
		return $html;
	}
}